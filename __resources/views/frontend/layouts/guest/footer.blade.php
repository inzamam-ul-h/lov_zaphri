<?php
$Site_Title = Site_Settings($Settings, 'site_title');

$Site_Phone = Site_Settings($Settings, 'phone');

$Site_Email = Site_Settings($Settings, 'email');

$Site_Address = Site_Settings($Settings, 'address');

$Site_About = Site_Settings($Settings, 'about_zaphry');

$Site_Facebook = Site_Settings($Settings, 'facebook');

$Site_Twitter = Site_Settings($Settings, 'twitter');

$Site_linkedin = Site_Settings($Settings, 'linkedin');

$Site_Youtube = Site_Settings($Settings, 'youtube');

$Site_Whatsapp = Site_Settings($Settings, 'whatsapp');

$Site_Dribble = Site_Settings($Settings, 'dribble');
?>
<footer id="ritekhela-footer" class="ritekhela-footer-one">

    <div class="ritekhela-footer-widget">
        <div class="container">
            <div class="row">
                <aside class="widget col-md-4 widget_about_info">
                    <a href="{{url('/home')}}">
                        <img src="{{ asset_url('images/logo_white.png') }}" alt="<?php echo $Site_Title; ?>">
                    </a>
                    <p><?php echo $Site_About; ?></p>

                </aside>
                <aside class="widget col-md-4 widget_about_info">
                    <div class="footer_widget_title"> 
                        <h2>Contact</h2> 
                    </div>
                    <ul>
                        <li><i class="fa fa-map-marker-alt"></i> <?php echo $Site_Address; ?></li>
                        <li><i class="fa fa-phone"></i> <?php echo $Site_Phone; ?></li>
                        <li><i class="fa fa-envelope"></i> <a href="#"><?php echo $Site_Email; ?></a></li>
                    </ul>
                </aside>
                <aside class="widget col-md-4 widget_gallery">
                    <div class="footer_widget_title"> <h2>Follow Us</h2> </div>

                    <div class="widget_about_info_social">
                        <a href="<?php echo $Site_Facebook; ?>" target="blank" class="fab fa-facebook-f"></a>
                        <a href="<?php echo $Site_Twitter; ?>" target="blank" class="fab fa-twitter"></a>
                        <a href="<?php echo $Site_Dribble; ?>" target="blank" class="fab fa-dribbble"></a>
                        <a href="<?php echo $Site_linkedin; ?>" target="blank" class="fab fa-linkedin-in"></a>
                        <a href="<?php echo $Site_Youtube; ?>" target="blank" class="fab fa-youtube"></a>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <div class="ritekhela-copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p>
                        Zaphri Designed & Developed by 
                        <a href="https://www.logic-valley.com" target="_blank">
                            Logic Valley (Pvt) Ltd.
                        </a>
                    </p>
                    <a href="#" class="ritekhela-back-top">
                        <i class="fa fa-angle-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</footer>