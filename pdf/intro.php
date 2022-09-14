<body>
<div id="background"> <img src="<?php echo  $img_back ?>" style="width: 508mm; height: 254mm; margin: 0;" /> </div>
<div id="foreground">
    <div id="intro_header">
        <p class="congrats">congrats <?php echo "<br>".$name;?>
            </p>
        <div id="entry-info">Ref:- <?=date("Y-m-d H:i:s T")?></div> 
    </div>
    <div id="intro_profile">
        <div id="profile_message">
            <p><strong>Today</strong> you are starting your journey to a healthier life. You feel ready for a change towards a more mindful approach to your nutrition and enjoy the results at last. <strong>Smart decision!</strong></p>
            <p><strong>We created this ebook for you</strong>, based on your profile and preferences. The plan is easy to follow and straight forward, filled with all the tools you need to make it happen.</p>
            <p>Let the transformation begin. <strong>ENJOY!</strong></p>
        </div>
        <div id="profile_info">
            <div class="profile_card goal_card">
                <div class="text">
                    <p class="profile_label">Your goal</p>
                    <p class="profile_value"><?php
                    //  $goals =  get_post_meta( $customer_id, 'goals', true );
                    //  
                           $goals =  get_field( 'goals', $customer_id );
                         echo implode(", ",$goals);    
                 
                     ?></p>
                   
                </div>
            </div>
            <div class="profile_card weight_card">
                <div class="text">
                    <p class="profile_label">Ideal weight</p>
                    <p class="profile_value"><?php
                    $weight = get_post_meta( $customer_id, 'desired_weight_mt', true );
                    if( empty($weight)){
                        $weight = get_post_meta( $customer_id, 'desired_weight_imperial', true );
                        echo $weight. ' lb';                       
                    }else{
                        echo $weight. ' kg';
                    }

                     ?></p>
                </div>
            </div>
            <div class="profile_card restrictions_card">
                <div class="text">
                    <p class="profile_label">Restrictions</p>
                    <p class="profile_value"><?php
                    // $restrictions =  get_post_meta( $customer_id, 'restrictions', true );
                     $restrictions =  get_field( 'restrictions', $customer_id );
                     echo implode(", ",$restrictions);  
                   ?></p>
                </div>
                </div>
            <div class="profile_card habits_card">
                <div class="text">
                    <p class="profile_label">Habits</p>
                    <p class="profile_value"><?php
                     $habits =  get_field( 'habits', $customer_id );
                    echo implode(", ",$habits); 
                ?></p>
                </div>
            </div>
            <div class="profile_card sleep_card">
                <div class="text">
                    <p class="profile_label">Hours of sleep</p>
                    <p class="profile_value"><?php
                    $sleep =  get_post_meta( $customer_id, 'sleep', true );
                    echo $sleep; 
                //    $sleep =  get_field( 'sleep', $customer_id );
                //    echo esc_attr($sleep['label']);
            
                ?></p>
                  
                </div>
            </div>
            <div class="profile_card fitness_card">
                <div class="text">
                    <p class="profile_label">Fitness level</p>
                    <p class="profile_value"><?php
                           $exercise =  get_post_meta( $customer_id, 'exercise', true );
                           echo $exercise; 
                        // $exercise =  get_field( 'exercise', $customer_id );
                        // echo esc_attr($exercise['label']);
                     ?></p>         
                </div>
            </div>
            <div class="profile_card day_card">
                <div class="text">
                     <p class="profile_label">Typical Day</p>
                     <p class="profile_value"><?php
                         $health =  get_post_meta( $customer_id, 'health', true );
                         echo $health; 
                    //   $health =  get_field( 'health', $customer_id );
                    //   echo esc_attr($health['label']);
                     ?></p>      
                </div>
            </div>
            <div class="profile_card protein_card">
                <div class="text">
                <p class="profile_label">Animal Protein</p>
                <p class="profile_value"><?php
                 $protein =  get_post_meta( $customer_id, 'protein', true );
                 if(is_array($protein)){
                    echo implode(", ",$protein);
                 }else{
                    echo $protein;
                 }
                
                ?></p>
            </div>
        </div>
    </div>
</div>
</body>
