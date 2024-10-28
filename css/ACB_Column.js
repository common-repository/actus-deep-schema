import { ACB_Components as ACBC } from './components.js';
// ------------------------------------------------------
let withSelect	= window.wp.data.withSelect,
	el 			= window.wp.element.createElement,
	BE 		  	= window.wp.blockEditor,
	CO 		  	= window.wp.components;
// ------------------------------------------------------
// AREA
// ------------------------------------------------------
// console.log('===================== ACB Column');



let ACB_Column = {
	attributes 	: {
		pad_t: { type: 'string', default: '32px' },
		pad_r: { type: 'string', default: '32px' },
		pad_b: { type: 'string', default: '32px' },
		pad_l: { type: 'string', default: '32px' },
		padStatus: { type: 'boolean', default: false },
		
		widths : { type: 'object', default: {
				wide	: null,
				desktop : null,
				laptop	: null,
				tablet	: null,
				mobile	: null,
			}
	 	}
	},
	example 	: {
		attributes: {
			title: 'Δοκιμαστικός Τίτλος',
		}
	},
	ALLOWED_BLOCKS : [
		'core/paragraph', 'core/columns', 'core/column', 'core/image'
	],
	
	register: function(){
		window.wp.blocks.registerBlockType( 'actus/acb-column', {
			title: 'ACT Column',
			icon: 'align-full-width', 
			category: 'actus', // formatting
			parent: [ 'actus/acb-columns', 'actus/acb-carousel' ],
			
			attributes: ACB_Column.attributes,
			
			example: ACB_Column.example,

			edit: function( props ) {
				return (
					el( window.wp.element.Fragment, {},
						ACB_Column.PANEL( props ),
						ACB_Column.EDIT( props )
					)
				);
			},
			
			save: function( props ) {
				return ACB_Column.SAVE( props );
			},
		
		})
	},
	PANEL : function( props ){
		var ATT = props.attributes;
		
		return [
			el( BE.InspectorControls, {},
			   

				el( CO.PanelBody, { title: 'Position', initialOpen: true },
				   
					el( CO.ToggleControl, {
						label: 'PADDING',
						onChange: (v)=>{ props.setAttributes({ padStatus: v }); },
						checked: ATT.padStatus,
						className: 'ACB-half',
					}),
				   
				    ATT.padStatus ?
					ACBC.Padding(props, '32px', '32px') : '',
				   
				),

			   

			)
	  ];
	},
	EDIT : function( props ){
		var ATT = props.attributes;
		props.className = props.className || '';
		
		
		let styles =  {}
		if ( ATT.padStatus ) {
			styles.padding = `calc(${ATT.pad_t} / 2) calc(${ATT.pad_r} / 2) calc(${ATT.pad_b} / 2) calc(${ATT.pad_l} / 2)`;
		}
		
		
		return [ 
			el( 'div', { className: props.className + ' Acolumn', style: styles },
				el( BE.InnerBlocks )
			)
		];
	},
	SAVE : function( props ){
		var ATT = props.attributes;
		props.className = props.className || '';
		
		
		let data = {
			className: props.className + ' Acolumn',
			style: ACB_Column.widthStyle( ATT )
		}
		
		return (
			
			el( 'div', data,
				el( BE.InnerBlocks.Content ),
			)
		);
	},
	 
	widthStyle : function(ATT){
		
		let styles = { '--flex' : '1' }
		if ( ATT.widths.wide ) styles['--flex']			= '1 1 auto';
		if ( ATT.widths.wide ) styles['--acb-w-wide'] 		= ATT.widths.wide + '%';
		if ( ATT.widths.desktop ) styles['--acb-w-desktop']	= ATT.widths.desktop + '%';
		if ( ATT.widths.laptop ) styles['--acb-w-laptop']	= ATT.widths.laptop + '%';
		if ( ATT.widths.tablet ) styles['--acb-w-tablet']	= ATT.widths.tablet + '%';
		if ( ATT.widths.mobile ) styles['--acb-w-mobile']	= ATT.widths.mobile + '%';
		styles['--acb-pad'] = 'initial';
		if ( ATT.padStatus ) {
			styles['--acb-pad'] = `${ATT.pad_t} ${ATT.pad_r} ${ATT.pad_b} ${ATT.pad_l}`;
		}
		
		return styles;
		
	},


}



// ------------------------------------------------------
export { ACB_Column }