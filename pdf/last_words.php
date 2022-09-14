<?php
 //$pdf->imageVars['intro_header'] = file_get_contents(__DIR__.'/assets/44-last_words-keto.png');
?>
<body>
    <div id="background"> <img src="<?php echo  $img_back ?>" style="width: 508mm; height: 254mm; margin: 0;" /> </div>
    <div id="foreground">
        <div id="intro_header">
            <div id="copyright">
                <p class="book-title"><strong>Your Ketogenic Meal Plan</strong></p>
                <p>Copyright &copy; 2020 Ketoday365</p>
                <p>All rights reserved.</p>
                <p>This ebook and its constituent parts are intended for personal use by the customer who purchased it at our website. It may not be reproduced in any form or transmitted in any form by any means—electronic, mechanical, photocopy, recording, or otherwise—without prior written permission of ketoday365, except as provided by the United States of America copyright law. For permission requests, please write to us.</p>
                <p>For further questions regarding this ebook or customer assistance, please write to <strong><a href="mailto:support@ketoday365.com">support@ketoday365.com</a></strong> or visit our website's contact page:</p>
                <p><strong><a href="http://www.ketoday365.com/contact-support/">www.ketoday365.com/contact-support/</a></strong></p>
                <p class="entry-info">REF: <?=$mpl_customer_id?> - <?=date("Y-m-d H:i:s T")?></p>
            </div>
        </div>
    </div>
</body>
