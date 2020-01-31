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

		// Include file system.
		$this->include_filesystem();

		$upload_dir = $this->get_upload_dir() . '/seaman';
		$filename   = "{$upload_dir}/post-{$request['id']}.xlsx";
		// if ( $wp_filesystem->exists( $filename ) ) {
		// 	return [ 'hey' ];
		// }

		$wp_filesystem->mkdir( $upload_dir );
		require_once BZALPHA_INC . 'spreadsheet/seaman.php';
		$file = new \BZAlpha\Spreadsheet\Seaman( $request['id'], $filename );

		return [];
	}

	/**
	 * Get upload dir.
	 */
	private function get_upload_dir() {
		global $wp_filesystem;

		// Include file system.
		$this->include_filesystem();

		$upload_dir = wp_get_upload_dir();
		$dir        = $upload_dir['basedir'] . '/bzalpha';
		$wp_filesystem->mkdir( $dir );

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
