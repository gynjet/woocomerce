<?php
/**
 * @package WPSEO_Local\Frontend\Schema
 */

/**
 * Class WPSEO_Local_Schema_IDs.
 *
 * Defines all `@id` hashes we need throughout Local SEO's Schema.
 *
 * @property WPSEO_Schema_Context $context A value object with context variables.
 * @property array                $options Local SEO options.
 */
class WPSEO_Local_Schema_IDs {

	/**
	 * @var string
	 */
	const PLACE_ID = '#local-place';

	/**
	 * @var string
	 */
	const ADDRESS_ID = '#local-place-address';

	/**
	 * @var string
	 */
	const ORGANIZATION_ID = '#local-organization';

	/**
	 * @var string
	 */
	const ORGANIZATION_LOGO = '#local-org-logo';

	/**
	 * @var string
	 */
	const LIST_ID = '#list';
}
