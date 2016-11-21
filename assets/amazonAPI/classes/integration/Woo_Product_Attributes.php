<?php
/**
 * Display product condition and notes as attributes in the product page
 */
class WPLA_Product_Attributes {

    /**
     * Register hooks
     */
    public function __construct() {
        if ( WPLA_SettingsPage::getOption( 'display_condition_and_notes', 0 ) == 1 ) {
            add_filter( 'woocommerce_get_product_attributes', array( $this, 'addProductAttributes' ) );
        }
    }

    /**
     * Add item condition and item note as attributes
     * @param array $attributes
     * @return array
     */
    public function addProductAttributes( $attributes = array() ) {
        global $product;

        if ( !is_object( $product ) ) {
            return $attributes;
        }

        $condition  = get_post_meta( $product->id, '_amazon_condition_type', true );
        $note       = get_post_meta( $product->id, '_amazon_condition_note', true );

        if ( $condition ) {
            $condition = self::getConditionString( $condition );

            $attributes[] = array(
                'is_visible'    => true,
                'is_taxonomy'   => false,
                'name'          => __('Condition', 'wpla'),
                'value'         => $condition
            );
        }

        if ( $note ) {
            $attributes[] = array(
                'is_visible'    => true,
                'is_taxonomy'   => false,
                'name'          => __('Note', 'wpla'),
                'value'         => $note
            );
        }

        return $attributes;
    }

    /**
     * Return a readable string from the given $conditionType
     *
     * @param string $conditionType
     * @return string
     */
    public static function getConditionString( $conditionType ) {
        $string = $conditionType;
        $map = array(
            'New'                   => __('New', 'wpla'),
            'UsedLikeNew'           => __('Used - Like New', 'wpla'),
            'UsedVeryGood'          => __('Used - Very Good', 'wpla'),
            'UsedGood'              => __('Used - Good', 'wpla'),
            'UsedAcceptable'        => __('Used - Acceptable', 'wpla'),
            'Refurbished'           => __('Refurbished', 'wpla'),
            'CollectibleLikeNew'    => __('Collectible - Like New', 'wpla'),
            'CollectibleVeryGood'   => __('Collectible - Very Good', 'wpla'),
            'CollectibleGood'       => __('Collectible - Good', 'wpla'),
            'CollectibleAcceptable' => __('Collectible - Acceptable', 'wpla'),
        );

        if ( isset( $map[ $conditionType ] ) ) {
            $string = $map[ $conditionType ];
        }

        return $string;
    }

}