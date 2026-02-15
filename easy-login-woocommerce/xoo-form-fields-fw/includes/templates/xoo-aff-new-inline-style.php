<?php


$iconbgcolor 	= $sy_options['s-icon-bgcolor'];
$iconcolor 		= $sy_options['s-icon-color'];
$iconsize 		= $sy_options['s-icon-size'];
$iconwidth 		= $sy_options['s-icon-width'];
$fieldmargin 	= $sy_options['s-field-bmargin'];
$inputbgcolor 	= $sy_options['s-input-bgcolor'];
$inputtxtcolor 	= $sy_options['s-input-txtcolor'];
$focusbgcolor 	= $sy_options['s-input-focusbgcolor'];
$focustxtcolor 	= $sy_options['s-input-focustxtcolor'];
$inputheight 	= $sy_options['s-input-height'];
$reqiredSymbol 	= $sy_options['s-show-reqicon'];
$iconAlign 		= $sy_options['s-icon-align'];


$inputBorderCSS 		= $ff_helper->get_border_css_value( $sy_options['s-input-border'] );
$inputBorderFocus 		= $ff_helper->get_border_css_value( $sy_options['s-input-border-focus'] );
$inputIconColorFocus 	= $sy_options['s-icon-color-focus'];
$inputFsize  			= $sy_options['s-input-fsize'];
$labelFsize  			= $sy_options['s-label-fsize'];
$inputIconBGColorFocus 	= $sy_options['s-icon-bgcolor-focus'] ?: 'transparent';
$inputIconPadding 		= (int) $iconwidth + ( $iconbgcolor ? 10 : 0 );
$showIcons 				= $sy_options['s-show-icons'] === "yes";


?>


.xoo-aff-input-group .xoo-aff-input-icon{
	background-color: <?php echo esc_html( $iconbgcolor ); ?>;
	color: <?php echo esc_html( $iconcolor ); ?>;
	max-width: <?php echo esc_html( $iconwidth ); ?>px;
	min-width: <?php echo esc_html( $iconwidth ); ?>px;
	font-size: <?php echo esc_html( $iconsize ); ?>px;
    position: absolute;
    z-index: 4;
    top: 0;
    bottom: 0;
    <?php if( $iconbgcolor ): ?>
    	<?php echo $inputBorderCSS ?>
	<?php else: ?>
		border: 0;
		border-radius: 0;
	<?php endif; ?>

	<?php echo esc_html( $iconAlign ); ?>: 0;

}


<?php if( $showIcons && $iconAlign === 'right' && !is_rtl() ): ?>
.xoo-aff-pw-toggle {
    right: <?php echo (int) $iconwidth + 5 ?>px;
}
<?php endif; ?>

<?php if( $showIcons && $iconAlign === 'left' && is_rtl() ): ?>
body.rtl .xoo-aff-pw-toggle {
    left: <?php echo (int) $iconwidth + 5 ?>px;
}
<?php endif; ?>



.xoo-aff-isfocused.xoo-aff-group .xoo-aff-input-icon{
	color: <?php echo esc_html( $inputIconColorFocus ); ?>;
	background-color: <?php echo esc_html( $inputIconBGColorFocus ); ?>;
	<?php echo esc_html( $inputBorderFocus ) ?>
}

.xoo-aff-isfocused.xoo-aff-group .xoo-aff-input-icon, .xoo-aff-input-group .xoo-aff-input-icon{
	<?php if( $iconAlign === 'left' ): ?>

	    border-right: 0;
	    border-bottom-right-radius: 0;
	    border-top-right-radius: 0;

	<?php else: ?>

		border-left: 0;
	    border-bottom-left-radius: 0;
	    border-top-left-radius: 0;

	<?php endif; ?>
}

.xoo-aff-group{
	margin-bottom: <?php echo esc_html( $fieldmargin ); ?>px;
}

.xoo-aff-group label{
	font-size: <?php echo (int) $labelFsize ?>px;
}

.xoo-aff-group input[type="text"], .xoo-aff-group input[type="password"], .xoo-aff-group input[type="email"], .xoo-aff-group input[type="number"], .xoo-aff-group select, .xoo-aff-group select + .select2, .xoo-aff-group input[type="tel"], .xoo-aff-group input[type="file"]{
	background-color: <?php echo esc_html( $inputbgcolor ); ?>;
	color: <?php echo esc_html( $inputtxtcolor ); ?>;
	height: <?php echo (int) $inputheight; ?>px;
	line-height: <?php echo (int) $inputheight; ?>px;
	padding: 12px;
	font-size: <?php echo (int) $inputFsize; ?>px;
	<?php echo $inputBorderCSS ?>
}

.xoo-aff-input-group input[type="text"], .xoo-aff-input-group input[type="password"], .xoo-aff-input-group input[type="email"], .xoo-aff-input-group input[type="number"], .xoo-aff-input-group select, .xoo-aff-input-group select + .select2, .xoo-aff-input-group input[type="tel"], .xoo-aff-input-group input[type="file"]{
	padding-left: <?php echo $iconAlign === 'left' && $showIcons ? $inputIconPadding : 12 ?>px;
	padding-right: <?php echo $iconAlign === 'right' && $showIcons ? $inputIconPadding : 12 ?>px;
}


.xoo-aff-group input[type="file"]{
	line-height: calc(<?php echo (int) $inputheight; ?>px - 24px);
}



.xoo-aff-group input[type="text"]::placeholder, .xoo-aff-group input[type="password"]::placeholder, .xoo-aff-group input[type="email"]::placeholder, .xoo-aff-group input[type="number"]::placeholder, .xoo-aff-group select::placeholder, .xoo-aff-group input[type="tel"]::placeholder, .xoo-aff-group .select2-selection__rendered, .xoo-aff-group .select2-container--default .select2-selection--single .select2-selection__rendered, .xoo-aff-group input[type="file"]::placeholder, .xoo-aff-group input::file-selector-button{
	color: <?php echo esc_html( $inputtxtcolor ); ?>;
}

.xoo-aff-group input[type="text"]:focus, .xoo-aff-group input[type="password"]:focus, .xoo-aff-group input[type="email"]:focus, .xoo-aff-group input[type="number"]:focus, .xoo-aff-group select:focus, .xoo-aff-group select + .select2:focus, .xoo-aff-group input[type="tel"]:focus, .xoo-aff-group input[type="file"]:focus{
	background-color: <?php echo esc_html( $focusbgcolor ); ?>;
	color: <?php echo esc_html( $focustxtcolor ) ?>;
	<?php echo esc_html( $inputBorderFocus ) ?>
}

[placeholder]:focus::-webkit-input-placeholder{
	color: <?php echo esc_html( $focustxtcolor ) ?>!important;
}

<?php if( !$showIcons ): ?>

	.xoo-aff-input-group .xoo-aff-input-icon, .xoo-aff-pw-toggle{
		display: none!important;
	}

<?php endif; ?>



<?php if( $reqiredSymbol === "yes" ): ?>

	.xoo-aff-cont-required:after {
	    content: "*";
	    position: absolute;
	    right: 5px;
	    top: 2px;
	    z-index: 10;
	    font-weight: 600;
	    opacity: 0.5;
	}

	body.rtl .xoo-aff-cont-required:after{
		left: 5px;
		right: auto;
	}

<?php endif; ?>