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
		$this->spreadsheet->getDefaultStyle()->getFont()->setName( 'Calibri' );
		$this->spreadsheet->getDefaultStyle()->getFont()->setSize( 10 );
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
				'vertical' => Style\Alignment::VERTICAL_CENTER,
				'wrapText' => true,
			],
			'fill' => [
				'fillType'   => Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FF99CCFF',
				],
			],
		];

		$styles['label_centered'] = [
			'alignment' => [
				'vertical'   => Style\Alignment::VERTICAL_CENTER,
				'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
				'wrapText'   => true,
			],
			'fill' => [
				'fillType'   => Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FF99CCFF',
				],
			],
		];

		$this->cell( 'F1:Y3', 'By signing this application form you agree that BZ ALPHA NAVIGATION INC. may collect, use and disclose your personal data as provided in this application form in accordance with the Personal Data Protection Act 2012 of the Philippines.' );

		$this->cell( 'F4:I4', 'Position', $styles['label'] );

		$this->cell( 'J4:O4', $this->get_meta( 'rank' ) );

		$this->cell( 'P4:S4', 'Date Available', $styles['label'] );

		$this->cell( 'T4:Y4', $this->get_date_meta( 'date_available' ) );

		$this->cell( 'F5:I5', 'Surname', $styles['label'] );

		$this->cell( 'J5:Y5', $this->get_meta( 'last_name' ) );

		$this->cell( 'F6:I6', 'Name', $styles['label'] );

		$this->cell( 'J6:Y6', $this->get_meta( 'first_name' ) );

		$this->cell( 'F7:I7', 'Date of Birth', $styles['label'] );

		$this->cell( 'J7:O7', $this->get_date_meta( 'birth_date' ) );

		// TODO
		$this->cell( 'P7:S7', 'Father Name', $styles['label'] );

		$this->cell( 'T7:Y7', '' );

		$this->cell( 'F8:I8', 'Place of Birth', $styles['label'] );

		$this->cell( 'J8:O8', $this->get_meta( 'birth_place' ) );

		// TODO
		$this->cell( 'P8:S8', 'Mother Name', $styles['label'] );

		$this->cell( 'T8:Y8', '' );

		$this->cell( 'F9:I9', 'Nationality', $styles['label'] );

		$this->cell( 'J9:O9', $this->get_meta( 'nationality' ) );

		$this->cell( 'P9:S9', 'Marital Status', $styles['label'] );

		$this->cell( 'T9:Y9', $this->get_meta( 'marital_status' ) );

		$this->cell( 'A11:D11', 'Phone', $styles['label'] );

		$this->cell( 'E11:L11', $this->get_meta( 'phone' ) );

		$this->cell( 'A12:D12', 'Email', $styles['label'] );

		$this->cell( 'E12:L12', $this->get_meta( 'email' ) );

		$this->cell( 'A13:D13', 'Skype', $styles['label'] );

		$this->cell( 'E13:L13', $this->get_meta( 'skype' ) );

		// TODO
		$this->cell( 'M11:P13', 'Living Address', $styles['label_centered'] );

		// TODO
		$this->cell( 'A14:D14', 'Next Kin', $styles['label'] );

		$this->cell( 'E14:L14', '' );

		// TODO
		$this->cell( 'A15:D15', 'Relation', $styles['label'] );

		$this->cell( 'E15:L15', '' );

		// TODO
		$this->cell( 'A16:D16', 'Phone (Kin)', $styles['label'] );

		$this->cell( 'E16:L16', '' );

		// TODO
		$this->cell( 'M14:P16', 'Registration Address', $styles['label_centered'] );

		$this->cell( 'A18:L18', 'Last 7 years Sea Service Data (Fill in block letters)', $styles['label_centered'] );
		$this->cell( 'A19:L19', 'Vessel\'s name / Year / Flag / Shipowner\'s name / Country', $styles['label_centered'] );
		$this->cell( 'M18:N19', 'Rank', $styles['label_centered'] );
		$this->cell( 'O18:P18', 'From', $styles['label_centered'] );
		$this->cell( 'O19:P19', 'Till', $styles['label_centered'] );
		$this->cell( 'Q18:T18', 'Vessel\'s Type', $styles['label_centered'] );
		$this->cell( 'Q19:T19', 'TEU (cont only)', $styles['label_centered'] );
		$this->cell( 'U18:V18', 'DWT', $styles['label_centered'] );
		$this->cell( 'U19:V19', 'GRT', $styles['label_centered'] );
		$this->cell( 'W18:Y18', 'Engine Type', $styles['label_centered'] );
		$this->cell( 'W19:Y19', 'HP', $styles['label_centered'] );
		$this->cell( 'Z18:AD18', 'Crewing', $styles['label_centered'] );
		$this->cell( 'Z19:AD19', 'Wage', $styles['label_centered'] );
	}

	/**
	 * Create cell.
	 */
	public function cell( $range, $value = null, $styles = [] ) {
		$cells = explode( ':', $range );

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
