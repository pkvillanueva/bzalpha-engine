<?php

namespace BZAlpha\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;

/**
 * Export Controller.
 */
class Seaman {

	/**
	 * Spreadsheet.
	 */
	protected $spreadsheet;

	/**
	 * Sheet.
	 */
	protected $sheet;

	/**
	 * Post object.
	 */
	protected $post;

	/**
	 * Filename.
	 */
	protected $filename;

	/**
	 * Constructor.
	 */
	public function __construct( $post_id, $filename ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'post_not_found', __( 'Post not found.', 'bzalpha' ) );
		}

		$this->spreadsheet = new Spreadsheet();
		$this->sheet       = $this->spreadsheet->getActiveSheet();
		$this->post        = $post;
		$this->filename    = $filename;
		$this->generate();
	}

	/**
	 * Generate file.
	 */
	public function generate() {
		$this->settings();
		$this->template();
		$this->styles();

		$writer = new Xlsx( $this->spreadsheet );
		$writer->save( $this->filename );
	}

	/**
	 * Sheet settings.
	 *
	 * @return void
	 */
	public function settings() {
		$this->sheet->getDefaultColumnDimension()->setWidth( 4 );
		$this->sheet->getDefaultRowDimension()->setRowHeight( 18 );
		$this->sheet->getPageSetup()->setFitToWidth( 1 );
		$this->sheet->getPageSetup()->setFitToHeight( 0 );
	}

	/**
	 * Sheet template.
	 */
	public function template() {
		$styles['label'] = [
			'alignment' => [
				'vertical'   => Style\Alignment::VERTICAL_CENTER,
				'wrapText'   => true,
			],
			'fill' => [
				'fillType'   => Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FF99CCFF',
				],
			],
		];

		$cells = [
			[ 'F1:U3',   'By signing this application form you agree that BZ ALPHA NAVIGATION INC. may collect, use and disclose your personal data as provided in this application form in accordance with the Personal Data Protection Act 2012 of the Philippines.' ],
			[ 'F4:I4',   [ 'Position',       [ 'J4:M4', $this->get_meta( 'rank' ) ] ],                $styles['label'] ],
			[ 'N4:Q4',   [ 'Date Available', [ 'R4:U4', $this->get_date_meta( 'date_available' ) ] ], $styles['label'] ],
			[ 'F5:I5',   [ 'Surname',        [ 'J5:U5', $this->get_meta( 'last_name' ) ] ],           $styles['label'] ],
			[ 'F6:I6',   [ 'Name',           [ 'J6:U6', $this->get_meta( 'first_name' ) ] ],          $styles['label'] ],
			[ 'F7:I7',   [ 'Date of Birth',  [ 'J7:M7', $this->get_date_meta( 'birth_date' ) ] ],     $styles['label'] ],
			[ 'N7:Q7',   [ 'Father Name',    [ 'R7:U7' ] ],                                           $styles['label'] ],
			[ 'F8:I8',   [ 'Place of Birth', [ 'J8:M8', $this->get_meta( 'birth_place' ) ] ],         $styles['label'] ],
			[ 'N8:Q8',   [ 'Mother Name',    [ 'R8:U8' ] ],                                           $styles['label'] ],
			[ 'F9:I9',   [ 'Nationality',    [ 'J9:M9', $this->get_meta( 'nationality' ) ] ],         $styles['label'] ],
			[ 'N9:Q9',   [ 'Marital Status', [ 'R9:U9', $this->get_meta( 'marital_status' ) ] ],      $styles['label'] ],
			[ 'A11:D11', [ 'Phone',          [ 'E11:L11', $this->get_meta( 'phone' ) ] ],             $styles['label'] ],
			[ 'A12:D12', [ 'Email',          [ 'E12:L12', $this->get_meta( 'email' ) ] ],             $styles['label'] ],
			[ 'A13:D13', [ 'Skype',          [ 'E13:L13', $this->get_meta( 'skype' ) ] ],             $styles['label'] ],
			[ 'M11:P13', [ 'Living Address', '' ],                                                    $styles['label'] ],
			[ 'A14:D14', [ 'Next Kin',       [ 'E14:L14', '' ] ],                                     $styles['label'] ],
			[ 'A15:D15', [ 'Relation',       [ 'E15:L15', '' ] ],                                     $styles['label'] ],
			[ 'A16:D16', [ 'Phone (Kin)',    [ 'E16:L16', '' ] ],                                     $styles['label'] ],
			[ 'M14:P16', [ 'Registration Address', '' ],                                              $styles['label'] ],
			[ 'A18:M18', [ 'Last 7 years Sea Service Data (Fill in block letters)', '' ],             $styles['label'] ],
			[ 'A19:M19', [ 'Vessel\'s name / Year / Flag / Shipowner\'s name / Country', '' ],        $styles['label'] ],
			[ 'N18:O19', [ 'Rank', '' ],                                                              $styles['label'] ],
			[ 'P18:R18', [ 'From', '' ],                                                              $styles['label'] ],
			[ 'P19:R19', [ 'Till', '' ],                                                              $styles['label'] ],
		];

		foreach ( $cells as $cell ) {
			call_user_func_array( [ $this, 'cell' ], $cell );
		}
	}

	/**
	 * Create cell.
	 */
	public function cell( $range, $value = null, $styles = [] ) {
		$cells = explode( ':', $range );

		if ( is_array( $value ) && isset( $value[1] ) ) {
			call_user_func_array( [ $this, 'cell' ], $value[1] );
			$value = $value[0];
		}

		$this->sheet->mergeCells( $range );
		$this->sheet->setCellValue( $cells[0], $value );
		$this->sheet->getStyle( $range )->applyFromArray( $styles );
	}

	/**
	 * Sheet styles.
	 */
	public function styles() {
		$this->sheet->getStyle( 'F1:U4' )->getAlignment()->setWrapText( true );
		$this->sheet->getStyle( 'F1:U4' )->getAlignment()->setVertical( Style\Alignment::VERTICAL_CENTER );
	}

	/**
	 * Get meta.
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->post->ID, $key, true );
	}

	/**
	 * Get date meta.
	 */
	public function get_date_meta( $key ) {
		$value = $this->get_meta( $key );
		return ! empty( $value ) ? date( "d-m-Y", strtotime( $value ) ) : null;
	}
}
