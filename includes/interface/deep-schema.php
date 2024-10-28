<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// ACTUS Deep Schema page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_deep_schema_page(){
	global $ACSC;
	$logo = ACSC_URL . 'img/acsc_logo.png';
?>
	<div class="wrap act-admin acsc-admin">

		<h2></h2>

		<!-- HEADER -->
        <div class="act-admin-header acsc-header">
            <img class="act-admin-logo" src="<?php echo esc_url($logo); ?>">
			<h2 class="act-admin-title">Actus Deep Schema</h2>
        </div>
		
		
		
		
		
		<!-- MAIN -->
		<div class="act-admin-main ACSC-MAIN">
			<div class="actus-notes"></div>
		</div>

		
		

		<?php if ( isset($ACSC['sys']['urlParam']) ) { ?>
			<div class="acsc-activation-prompt">
				<a href="./admin.php?page=actus-deep-schema">Welcome to Actus Deep Schema!<br>click to continue</a>
			</div>
		<?php } ?>
		
        <!-- FOOTER -->
        <div class="act-admin-footer acsc-admin-footer">
            
			<div class="version">Actus Deep Schema v<b><?php echo esc_html(ACSC_VERSION); ?></b></div>
			
			<div class="mid">
				<img class="actus-logo" src="<?php echo esc_url(ACSC_URL . 'img/actus.png'); ?>">

				<div><a href="https://actus.works" target="_blank">ACTUS anima</a></div>
			</div>
			
            <div class="right"><a href="https://deepschema.org" target="_blank">deepschema.org</a></div>
        </div>
		

	</div>

	<style>
		.acsc-activation-prompt {
			text-align: center;
			padding: 32px;
			font-size: 24px;
			font-weight: 700;
			line-height: 1.2;
			background: hsl(250, 60%, 65%);
			box-shadow: 8px 8px 20px hsla(0, 0%, 0%, 0.56);
			border-radius: 0 0 12px 12px;
		}
		.acsc-activation-prompt a {
			text-decoration: none;
			color: white;
		}
		.acsc-admin * { box-sizing: border-box; }
		.act-admin-header.acsc-header img.act-admin-logo {
			position: relative;
			width: auto;
			height: 64px;
			padding: 8px;
			left: 0px;
			top: 0px;
		}
		.acsc-header {
			padding: 0 20px 0 8px;
			border: 0;
			border-radius: 12px 12px 0 0;
			display: flex;
			align-items: center;
			width: 100%;
			height: auto;
			position: relative;
			background: white;
			box-shadow: 8px 8px 20px hsla(0, 0%, 0%, 0.56);
		}
		.acsc-admin-footer {
			border-top: 3px solid var(--cc1);
			color: var(--cc1d);
			padding-top: 2px;
			height: auto;
			display: flex;
			position: relative;
			width: 100%;
			margin-top: 24px;
		}
		.acsc-admin-footer .actus-logo { height: 40px; width: auto; }
		.acsc-admin-header h2.act-admin-title {
			flex: 1;
			position: relative;
			margin: 0;
			font-size: 24px;
			text-align: right;
			color: black;
		}
		.acsc-admin > h2:first-child { padding: 0; }
		.act-admin-footer > * {
			flex: 0 0 auto;
			width: 33.333%;
		}
		.act-admin-footer .version {
			padding: 8px 0;
			text-align: left;
			opacity: 0.4;
			pointer-events: none;
		}
		.act-admin-footer .mid { text-align: center; }
		.act-admin-footer .right, .act-admin-footer .actus-sic {
			padding: 8px 0;
			text-align: right;
		}
	</style>
<?php
}





// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
acsc_deep_schema_page();
?>