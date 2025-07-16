<?php
/**
 * Sticky Elements Analytics Pro Feature
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (defined('ABSPATH') === false) {
    exit;
}
?>

<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" />
<div class="container  mystickyelement-widgetanalytic-wrap wrap">    
	<h2></h2>
    <div class="bg-white flex rounded-lg border border-solid border-[#EAEFF2] mystickyelement-widgetanalytic-body">
        <div class="px-7 py-8 flex-1">
            <h2 class="mystickyelement-widgetanalytic-heading"><?php _e("Unlock My Sticky Elements <span>Analytics</span> 🚀", "mystickyelements") ?></h2>
			
			<div class="flex items-center mt-5 space-x-3">
                <a class="btn rounded-lg drop-shadow-3xl font-normal" href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")) ?>" title="Upgrade to Pro">
                    <?php esc_html_e('Upgrade to Pro 🚀', 'mystickyelements'); ?>
                </a>                
            </div>
			
			<div class="mystickyelement-licenseimage">
				<img class="h-full w-auto" src="<?php echo esc_url(plugins_url('/images/analytics-image.png', __FILE__)); ?>" alt="StickyElements analytics" />
			</div>
			
			<h3><?php esc_html_e( 'What can you use it for?', 'mystickyelements');?></h3>
            <ul class="mt-7 flex flex-col space-y-2">
                <li class="flex items-center py-6 px-7 bg-[#F9FAFB] rounded-md space-x-6 text-cht-gray-150 text-lg font-primary">
                    <img width="42" height="59" src="<?php echo MYSTICKYELEMENTS_URL ?>/images/channel-discover.svg" alt="Channel Discover">
                    <span class="max-w-[305px]"><?php _e("<strong>Discover</strong> the most frequently used channels", "mystickyelements") ?></span>
                </li>
                <li class="flex items-center py-6 px-7 bg-[#F9FAFB] rounded-md space-x-6 text-cht-gray-150 text-lg font-primary">
                    <img width="42" height="59" src="<?php echo MYSTICKYELEMENTS_URL ?>/images/channel-tracking.svg" alt="Channel Tracking">
                    <span><?php _e("Keep <strong>track</strong> of how each widget performs", "mystickyelements") ?></span>
                </li>
                <li class="flex items-center py-6 px-7 bg-[#F9FAFB] rounded-md space-x-6 text-cht-gray-150 text-lg font-primary">
                    <img width="42" height="59" src="<?php echo MYSTICKYELEMENTS_URL; ?>/images/channel-analyze.svg" alt="Channel Analyze">
                    <span><?php _e("<strong>Analyze</strong> the number of unique clicks and the <strong>click-through rate</strong>", "mystickyelements") ?></span>
                </li>
            </ul>

            <div class="flex items-center mt-5 space-x-3">
                <a class="btn rounded-lg drop-shadow-3xl font-normal" href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")) ?>" title="Upgrade to Pro">
                    <?php esc_html_e('Upgrade to Pro 🚀', 'mystickyelements'); ?>
                </a>                
            </div>
        </div>
        
    </div>
</div>

<style>
.mystickyelement-widgetanalytic-body {
    display: flex;
	justify-content: space-evenly;	
}
.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h2.mystickyelement-widgetanalytic-heading {
	font-family: 'Lato';
	font-style: normal;
	font-weight: 800;
	font-size: 48px;
	line-height: 48px;
	text-align: center;
	color: #000000;
    display: block;
	margin: 40px auto;
    display: Block;
    justify-content: center;
    align-items: end;
	max-width: 500px;
}

.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h2.mystickyelement-widgetanalytic-heading span{
	color: #3C85F7;
	font-size: 48px;
	font-weight: 800;
}


.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h3{
	font-family: 'Lato';
	font-style: normal;
	font-weight: 600;
	font-size: 32px;
	line-height: 29px;
	color: #000000;
	text-align: center;
	margin:20px 0 16px;
}

/*.mystickyelement-widgetanalytic-body .w-auto{
	width:100%;
}*/

.mystickyelement-widgetanalytic-body ul.mt-7.flex.flex-col.space-y-2 {
    display: flex;
    flex-direction: column;
    margin-top: 1.75rem;
}

.mystickyelement-widgetanalytic-body img {
    height: auto;
    max-width: 100%;
    display: block;
    vertical-align: middle;
}

.mystickyelement-widgetanalytic-body li {
	flex-direction:column;
	padding:26px 35px 26px 35px;
	box-sizing: border-box;
	width: 282px;
	height: 153.26px;	
	background: #FFFFFF;
	border-top: 2px solid #DFDFFC;
	box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.08);
	border-radius: 16px;
	margin: 0px 10px 20px 10px;
	
	display:flex;
    font-size: 1.125rem;
    line-height: 1.75rem;
    align-items: center;
	
}
.mystickyelement-widgetanalytic-body ul.mt-7.flex.flex-col.space-y-2 {
	flex-direction:column;
	flex-flow:wrap;
	margin-bottom: 1.75rem;
}

.mystickyelement-widgetanalytic-body .mt-5{
	text-align:center;
	border-radius:8px;
	margin-top:3.25rem;
	margin-bottom:2.25rem;
}

.mystickyelement-widgetanalytic-body span{
	font-family: 'Lato';
	font-style: normal;
	font-weight: 400;
	font-size: 14px;
	line-height: 17px;
	text-align: center;
	margin-top:20px;
	margin-left: 0px;
	color: #000000;
    max-width: 405px;	
}

.mystickyelement-widgetanalytic-body a.btn.rounded-lg.drop-shadow-3xl.font-normal{
	padding:16px 47px 16px 47px;
	font-size:20px;
	text-align:center;	
	font-weight: 400;
    border-radius: 0.5rem;
    background-color: #3c85f7;
    color: #fff;    
    text-decoration-line: none;    
    line-height: 1.25rem;
	--tw-drop-shadow: drop-shadow(0px 9px 7px rgba60 133 247 /0.37%));
	border: 1px solid #3c85f7;
}

.mystickyelement-widgetanalytic-body ul li img{
	width:auto;
	height:48px;
}

.mystickyelement-widgetanalytic-body img.h-full.w-auto{
	display: flex;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
	width: auto;
	height:100%;
}
	

.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h2.mystickyelement-widgetanalytic-heading img{
	float:right;
}

@media screen and (max-width: 768px){
	.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h2.mystickyelement-widgetanalytic-heading span,
	.mystickyelement-widgetanalytic-body .px-7.py-8.flex-1 h2.mystickyelement-widgetanalytic-heading{
		font-size: 28px;
	}
	.mystickyelement-widgetanalytic-body li {
		margin: 0px 20px 20px 20px;
	}
}
</style>
