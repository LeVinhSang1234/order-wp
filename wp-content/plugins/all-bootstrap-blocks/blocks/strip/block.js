import * as areoi from '../_components/Core.js';
import meta from './block.json';

const ALLOWED_BLOCKS = null;

const BLOCKS_TEMPLATE = [
    [ 'areoi/container', {
        height_dimension_xs: '100',
        height_unit_xs: '%'
    }, [
        [ 'areoi/row', {
            height_dimension_xs: '100',
            height_unit_xs: '%',
            vertical_align: 'align-items-center'
        }, [
            [ 'areoi/column', {} ]
        ] ]
    ] ],
];

const blockIcon = <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H5V8h14v8z"/></svg>;

areoi.blocks.registerBlockType( meta, {
    icon: blockIcon,
    edit: props => {
        const {
            attributes,
            setAttributes,
            clientId
        } = props;

        const { block_id } = attributes;
        if ( !block_id ) {
            setAttributes( { block_id: clientId } );
        }

        const classes = [
            'strip',
            'align' + attributes.align,
            attributes.utilities_bg,
            attributes.utilities_text,
            attributes.utilities_border,
        ];

        const blockProps = areoi.editor.useBlockProps( {
            className: areoi.helper.GetClassName( classes ),
            style: { cssText: areoi.helper.GetStyles( attributes ) }
        } );

        function onChange( key, value ) {
            setAttributes( { [key]: value } );
        }

        const tabDevice = ( tab ) => {
            return (
                <div>
                    { areoi.DeviceLayout( areoi, attributes, onChange, tab ) }

                    { areoi.DeviceBackground( areoi, attributes, onChange, tab ) }
                </div>
            );
        };

        return (
            <>
                { areoi.DisplayPreview( areoi, attributes, onChange, 'strip' ) }

                { !attributes.preview &&
                    <div { ...blockProps } data-anchor={ attributes.anchor ? ' : #' + attributes.anchor : '' }>
                        <areoi.editor.BlockControls>
                            { areoi.Alignment( areoi, attributes, onChange ) }
                        </areoi.editor.BlockControls>
                        <areoi.editor.InspectorControls key="setting">
                            
                            { areoi.Utilities( areoi, attributes, onChange ) }

                            { areoi.Background( areoi, attributes, onChange ) }

                            { areoi.ResponsiveTabPanel( tabDevice, meta, props ) }
                                
                        </areoi.editor.InspectorControls>

                        { areoi.DisplayBackground( areoi, attributes ) }

                        <areoi.editor.InnerBlocks template={ BLOCKS_TEMPLATE } allowedBlocks={ ALLOWED_BLOCKS } />
                    </div>
                }
            </>
        );
    },
    save: () => { 
        return (
            <areoi.editor.InnerBlocks.Content/>
        );
    },
});