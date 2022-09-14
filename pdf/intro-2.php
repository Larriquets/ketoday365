<body id="intro-2">
    <div id="left">
        <div class="title-container" style="background-image: url('<?php echo  $img_back ?>');">
            <h1>CONGRATS <br><span> <?php echo "<br>" . $name; ?></span></h1>
        </div>
        <!-- <div id="woman" class="title-container">
            <h1>CONGRATS <br><span>NAME</span></h1>
        </div> -->
        <div id="welcome">
            <p><strong>Today</strong> you are starting your journey to a healthier life. You feel ready for a change
                towards a more
                mindful approach to your nutrition and enjoy the results at last. <strong>Smart decision!</strong>
            </p>
            <p><span>We created this meal plan for you</span>, based on your profile and preferences. The plan is
                easy to follow and straight forward, filled with all the tools you need to make it happen.</p>
            <p><strong>Let the transformation begin. ENJOY!</strong></p>
        </div>
    </div>
    <div id="right">
        <h2>Your Profile</h2>
        <div id="table">
            <div>
                <h3>GENDER</h3>
                <p class="profile_value"><?php
                                            $gender = '';
                                            $gender =  get_post_meta($customer_id, 'gender', true);
                                            echo $gender;
                                            ?></p>
            </div>
            <div>
                <h3>AGE</h3>
                <p class="profile_value"><?php
                                            $age = '';
                                            $age =  get_post_meta($customer_id, 'age', true);
                                            echo $age;
                                            ?></p>
            </div>
            <div>
                <h3>HEIGHT</h3>
                <p class="profile_value"><?php
                                            $height_cm = '';
                                            $height_cm =  get_post_meta($customer_id, 'height_cm', true);
                                            echo $height_cm;
                                            ?></p>
            </div>
        </div>
        <div class="icons">
            <div><img src="https://www.healthyfitplan.com/wp-content/uploads/goals.png" alt="Your Goal">
                <p class="profile_label">Your Goal</p>
                <p class="profile_value_pink"><?php
                                                $goals = '';
                                                $goals =  get_post_meta($customer_id, 'goals', true);
                                                echo $goals;
                                                ?></p>
            </div>
            <div><img src="https://www.healthyfitplan.com/wp-content/uploads/lose.png" alt="Weight to Lose">
                <p class="profile_label">Weight to Lose</p>
                <p class="profile_value_pink"><?php
                                                $weight = get_post_meta($customer_id, 'weight_loss_imp', true);
                                                if (empty($weight)) {
                                                    $weight = get_post_meta($customer_id, 'weight_loss_mt', true);
                                                    echo $weight . ' kg';
                                                } else {

                                                    echo $weight . ' lb';
                                                }

                                                ?></p>
            </div>
            <div class="no-border"><img src="https://www.healthyfitplan.com/wp-content/uploads/restrictions.png" alt="Restrictions">
                <p class="profile_label">Restrictions</p>
                <p class="profile_value_pink"><?php
                                                $restrictions =  '';
                                                $restrictions =  get_post_meta($customer_id, 'restrictions', true);
                                                echo $restrictions;
                                                ?></p>
            </div>
        </div>
        <div class="icons">
            <div><img src="https://www.healthyfitplan.com/wp-content/uploads/motivated.png" alt="How Motivated">
                <p class="profile_label">How Motivated</p>
                <p class="profile_value_pink"><?php
                                                $motivated =  get_field('motivated', $customer_id);
                                                echo esc_attr($motivated['label']);
                                                ?></p>
            </div>
            <div><img src="https://www.healthyfitplan.com/wp-content/uploads/fitness.png" alt="Fitness Level">
                <p class="profile_label">Fitness Level</p>
                <p class="profile_value_pink"><?php
                                                $exercise =  get_field('exercise', $customer_id);
                                                echo esc_attr($exercise['label']);
                                                ?></p>
            </div>
            <div class="no-border"><img src="https://www.healthyfitplan.com/wp-content/uploads/protein.png" alt="Animal Protein">
                <p class="profile_label">Animal Protein</p>
                <p class="profile_value_pink"><?php
                                                $protein =  get_post_meta($customer_id, 'protein', true);
                                                echo implode(", ", $protein);
                                                ?></p>
            </div>
        </div>
        <img id="mobile" src="https://www.healthyfitplan.com/wp-content/uploads/celular.png" alt="mobile">
    </div>
</body>