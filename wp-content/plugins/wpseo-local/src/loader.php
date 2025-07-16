<?php

namespace Yoast\WP\Local;

use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The Loader to handle initialization and integration.
 *
 * This loader is used by the generated container. During generation it is checked that the calls to methods
 * in this class are only called when the correct interface is implemented. This class does not verify that,
 * but any failure should become apparent during development.
 */
class Loader {

	/**
	 * The registered integrations.
	 *
	 * @var \Yoast\WP\SEO\Integrations\Integration_Interface[]
	 */
	protected $integrations = [];

	/**
	 * The registered initializers.
	 *
	 * @var \Yoast\WP\SEO\Initializers\Initializer_Interface[]
	 */
	protected $initializers = [];

	/**
	 * The container.
	 *
	 * @var ContainerInterface
	 */
	private $container;

	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Registers a class for initialization.
	 *
	 * This is called automatically by the generated container.
	 *
	 * @param string $class The fully qualified class name to register for initialization.
	 */
	public function register_initializer( $class ) {
		$this->initializers[] = $class;
	}

	/**
	 * Registers a class for integration.
	 *
	 * This is called automatically by the generated container.
	 *
	 * @param string $class The fully qualified class name to register for integration.
	 */
	public function register_integration( $class ) {
		$this->integrations[] = $class;
	}

	/**
	 * Loads all registered classes if their conditionals are met.
	 *
	 * This involves initialization and registering hooks, both only if needed.
	 *
	 * @return void
	 */
	public function load() {
		$this->load_initializers();
		$this->load_integrations();
	}

	/**
	 * Calls initialize on all classes for which the conditionals are met.
	 */
	protected function load_initializers() {
		foreach ( $this->initializers as $class ) {
			if ( $this->conditionals_are_met( $class ) ) {
				$this->container->get( $class )->initialize();
			}
		}
	}

	/**
	 * Calls register_hooks on all classes for which the conditionals are met.
	 */
	protected function load_integrations() {
		foreach ( $this->integrations as $class ) {
			if ( $this->conditionals_are_met( $class ) ) {
				$this->container->get( $class )->register_hooks();
			}
		}
	}

	/**
	 * Checks whether or not all registered conditionals are met.
	 *
	 * @param string|\Yoast\WP\SEO\Loadable_Interface $class The class name.
	 *
	 * @return bool True if all conditionals for the class return true, or if the class does not have conditionals
	 */
	protected function conditionals_are_met( $class ) {
		$conditionals = $class::get_conditionals();
		foreach ( $conditionals as $conditional ) {
			if ( ! $this->container->get( $conditional )->is_met() ) {
				return false;
			}
		}

		return true;
	}
}
