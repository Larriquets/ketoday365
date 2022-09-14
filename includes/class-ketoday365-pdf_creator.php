<?php
/**
 * WooCommerce Extension Boilerplate cart functions and filters.
 *
 * @class 	Funnel_Processing
 * @version 0.1.0
 * @since   0.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ketoday365_PDF_Creator {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {


	}


    function mpl_create_pdf_base ( $pdf_pages ,$product_name){
        $order_id =0;
        require_once __DIR__ . '/../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf($this->getPdfOptions());
            $this->setPdfConfigBAse( $mpdf, $order_id );
            
            foreach($pdf_pages as $pdf_page){  
                $res = wp_make_link_relative(  $pdf_page );
                $res = getcwd().$res ;
                $pagecount = $mpdf->SetSourceFile($res );
                for ($i=1; $i<=($pagecount); $i++) {
                    $mpdf->addPage('L','','','','',0,0,0,-1);	
                    $import_page = $mpdf->ImportPage($i);
                    $mpdf->UseTemplate($import_page);
                }
            }
            // $mpdf->Output();  
            $mpdf->Output($product_name.".pdf",\Mpdf\Output\Destination::DOWNLOAD);  
    }


    function mpl_create_pdf_user ( $pdf_pages,  $product_id , $mpl_customer_id ,$product_name){
            // require( get_stylesheet_directory()  . '/vendor/autoload.php');
            require_once __DIR__ . '/../vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf($this->getPdfOptions());
           // $mpdf = new \Mpdf\Mpdf();
            $this->setPdfConfig( $mpdf, $mpl_customer_id );
            $name = get_post_meta( $mpl_customer_id, 'name', true );
            $last_name = get_post_meta( $mpl_customer_id, 'last_name', true );
            $count = 0;
            foreach($pdf_pages as $pdf_page){        
                $res = wp_make_link_relative(  $pdf_page );
                $res = getcwd().$res ;
         
                if ($count == 0){
                    $gender = get_field("gender",$mpl_customer_id);
                    if ($gender == 'man') {
                        $img_back_cover = get_field( "cover_man",  $product_id  ); 
                        $img_back = get_field( "intro",  $product_id  ); 
                    }else{
                        $img_back_cover = get_field( "cover_woman",  $product_id  ); 
                        $img_back = get_field( "intro_woman",  $product_id  ); 
                    }
 
                    $this->mpl_add_php_cover($mpdf , $img_back_cover);
                     
                    $name = get_post_meta( $mpl_customer_id, 'name', true );
                    $quiz = get_post_meta( $mpl_customer_id, 'quiz', true );
                    $this->mpl_add_php_intro($mpdf, $img_back, $name,$mpl_customer_id,$quiz);  
               }

                $pagecount = $mpdf->SetSourceFile($res);

                for ($i=1; $i<=($pagecount); $i++) {
                    $mpdf->addPage('L','','','','',0,0,0,-1);
                    $import_page = $mpdf->ImportPage($i);
                    $mpdf->UseTemplate($import_page);
                }
                $count++;
            }		     
          $this->mpl_add_php_last_words($mpdf,$product_id,$mpl_customer_id );
            // try {
     
            //     $mpdf->Output('Keto Diet - '.$name.' '.$last_name.".pdf",\Mpdf\Output\Destination::INLINE); 
            // } catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
            //     // Process the exception, log, print etc.
            //     echo $e->getMessage();
            // }
            ob_clean();
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes'); 
            $mpdf->Output('Keto Diet - '.$name.' '.$last_name.".pdf",\Mpdf\Output\Destination::DOWNLOAD);  
            ob_end_flush();
    }


    function mpl_add_php_cover($mpdf , $img_back){
        
        $mpdf->addPage('L','','','','',0,0,0,-1);
        ob_start();
        include_once(__DIR__.'/../pdf/cover.php');
        $html = ob_get_contents();
        ob_clean();
        $mpdf->WriteHTML( $html );
    }


    function mpl_add_php_intro($mpdf , $img_back, $name ,$mpl_customer_id,$quiz){
        $customer_id =$mpl_customer_id;// get_post_meta( $mpl_customer_id, 'customer_id', true );
        $mpdf->addPage('L','','','','',0,0,0,-1);
        ob_start();
        if($quiz == 'Quiz_v2'){
            $gender =  get_post_meta( $customer_id, 'gender', true );
            if( $gender == 'man'){
                $img_back = 'https://myplan.ketoday365.com/wp-content/uploads/2021/05/imgMen.png';                     
            }else{
                $img_back = 'https://myplan.ketoday365.com/wp-content/uploads/2021/05/imgWoman.png';   
            }
            include_once(__DIR__.'/../pdf/intro-2.php');
        }else{
            include_once(__DIR__.'/../pdf/intro.php');            
        }

        $html = ob_get_contents();
        ob_clean();

        $stylesheet = file_get_contents(__DIR__.'/../pdf/assets/styles.css');
        $mpdf->WriteHTML( $stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS );
        $mpdf->WriteHTML( $html, \Mpdf\HTMLParserMode::HTML_BODY );
    }


    function mpl_add_php_last_words($mpdf, $product_id, $mpl_customer_id ){
        $img_back = get_field( "last_words",  $product_id  );  
        $mpdf->addPage('L','','','','',0,0,0,-1);
        ob_start();
        include_once(__DIR__.'/../pdf/last_words.php');
        $html = ob_get_contents();
        ob_clean();
        $mpdf->WriteHTML( $html, \Mpdf\HTMLParserMode::HTML_BODY );
    }


    function getPdfOptions(){
        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // list( $mode, $format, $default_font_size, $default_font, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $orientation ) = $this->initConstructorParams($config);
        return [
        /**
         * ATTENTION TO THIS: If 'debug' or 'allow_output_buffering' is set to true a Fatal Error is triggered on line 325 of wp-content/themes/wp-bootstrap-starter/functions.php file where an anonimous function is used with deprecated code.
         * It uses: add_filter( 'get_search_form', create_function( '$a', "return null;" ) );
         * It should use: add_filter( 'get_search_form', function( $a ){ return null } );
         */
        'debug' => false,
        'allow_output_buffering' => false,
        #############################################################
        'mode' => 'utf-8',
        'format' => [254, 508],
        'orientation' => 'L',

        // 'fontDir' => array_merge( $fontDirs, [ getMealPlanPartialsPath().'assets/fonts', ] ),
        'fontdata' => $fontData + [
            'lato' => [
            'R' => 'Lato-Light.ttf',
            'I' => 'Lato-Italic.ttf',
            'B' => 'Lato-Black.ttf',
            ]
        ],
        // 'default_font_size' => 16,
        // 'default_font' => 'Lato'
        ];
    }


    function setPdfConfig( &$pdf, $entry ){
        // $pdf->debug = true;
        $pdf->AddFontDirectory(__DIR__.'/../pdf/assets/fonts');

        $meal_plan = 'Meal Plan X ';//getRestrictionsAndProteinProfileByEntry( $entry->item_key );
     //   $pdf->debug = true;
        $pdf->SetAuthor('myplan.ketoday365.com');
        $pdf->SetCreator('HFP-mealPlanGenerator-0.3.5');
        $pdf->SetTitle('Your personalized Meal Plan');
        $pdf->SetSubject( 'Your Personalized ' ); ///.$meal_plan.' Meal Plan'. ( $entry->item_key ? ' - Entry: '.$entry->item_key : '' )
        // $pdf->SetKeywords($meal_plan.', '.$meal_plan.' meal plan, '.$meal_plan.' diet, healthyfitplan'.( $entry->item_key ? ', '.$entry->item_key : '' ).', '.getPlanByEntry( $entry->item_key ));

        // $pdf->useSubstitutions = false;
        $pdf->simpleTables = true;

        // $pdf->SetDefaultBodyCSS('color', '#000000');
        // $pdf->SetDefaultBodyCSS('font-family', 'Lato');
        // $pdf->SetDefaultBodyCSS('font-size', '16px');

        $pdf->SetDefaultFont('lato', 'R');

        // $pdf_user_permissions = array( 'copy', 'print', 'print-highres' );
        // $pdf_user_password = '';
        // $pdf_owner_password = 'u52B$hCrC*GAyS!EJ$jZc4bygdgU83ZF';
        // $pdf->SetProtection( $pdf_user_permissions, $pdf_user_password, $pdf_owner_password );
    }

    function setPdfConfigBAse( &$pdf, $entry ){
        // $pdf->debug = true;
        $pdf->AddFontDirectory(__DIR__.'/../pdf/assets/fonts');

        $meal_plan = 'Meal Plan X ';//getRestrictionsAndProteinProfileByEntry( $entry->item_key );

        $pdf->SetAuthor('myplan.ketoday365.com');
        $pdf->SetCreator('HFP-mealPlanGenerator-0.3.5');
        $pdf->SetTitle('Your personalized Meal Plan');
        $pdf->SetSubject( 'Your Personalized ' ); ///.$meal_plan.' Meal Plan'. ( $entry->item_key ? ' - Entry: '.$entry->item_key : '' )
        // $pdf->SetKeywords($meal_plan.', '.$meal_plan.' meal plan, '.$meal_plan.' diet, healthyfitplan'.( $entry->item_key ? ', '.$entry->item_key : '' ).', '.getPlanByEntry( $entry->item_key ));

        // $pdf->useSubstitutions = false;
        $pdf->simpleTables = true;

        // $pdf->SetDefaultBodyCSS('color', '#000000');
        // $pdf->SetDefaultBodyCSS('font-family', 'Lato');
        // $pdf->SetDefaultBodyCSS('font-size', '16px');

        $pdf->SetDefaultFont('lato', 'R');

    }
	

}