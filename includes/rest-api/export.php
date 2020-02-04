<?php

namespace BZAlpha\REST_API;

/**
 * Export Controller.
 */
class Export {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'routes' ] );
	}

	/**
	 * Register routes.
	 */
	public function routes() {
		register_rest_route( 'bzalpha/v1', '/export/seaman', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'export_seaman' ],
				'args'     => [
					'id' => [
						'description' => __( 'Set seaman ID.' ),
						'type'        => 'integer',
						'required'    => true,
					],
				]
			]
		] );
	}

	/**
	 * Export seaman.
	 */
	public function export_seaman( $request ) {
		global $wp_filesystem;

		// Get filedir.
		$upload_dir = $this->get_upload_dir() . '/seaman';

		// Create if not exists.
		$wp_filesystem->mkdir( $upload_dir );

		// Require generator.
		require_once BZALPHA_INC . 'spreadsheet/seaman.php';

		// Create instance.
		$file = new \BZAlpha\Spreadsheet\Seaman( $request['id'], $upload_dir );

		// Generate file.
		$file->generate();

		// Create url.
		$upload_url = $this->get_upload_dir( 'url' ) . '/seaman';

		return [
			'download' => "{$upload_url}/{$file->filename}",
		];
	}

	/**
	 * Get upload dir.
	 */
	private function get_upload_dir( $as = 'dir' ) {
		global $wp_filesystem;

		// Include file system.
		$this->include_filesystem();

		$upload_dir = wp_get_upload_dir();
		$source     = $as === 'url' ? 'baseurl' : 'basedir';
		$dir        = $upload_dir[ $source ] . '/bzalpha';

		if ( $as !== 'url' ) {
			$wp_filesystem->mkdir( $dir );
		}

		return $dir;
	}

	/**
	 * Include filesystem.
	 */
	private function include_filesystem() {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}
}
