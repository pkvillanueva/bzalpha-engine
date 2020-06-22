<?php

namespace BZAlpha\Spreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * Export Controller.
 */
class Seaman {

	/**
	 * Spreadsheet.
	 */
	protected $spreadsheet;

	/**
	 * Worksheet.
	 */
	protected $worksheet;

	/**
	 * Post object.
	 */
	protected $post;

	/**
	 * Filepath.
	 */
	public $filepath;

	/**
	 * Filename.
	 */
	public $filename;

	/**
	 * Constructor.
	 */
	public function __construct( $post_id, $filedir ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'post_not_found', __( 'Post not found.', 'bzalpha' ) );
		}

		$this->spreadsheet = IOFactory::load( BZALPHA_DIR . '/assets/seaman.xlsx' );
		$this->worksheet   = $this->spreadsheet->getActiveSheet();
		$this->post        = $post;

		$last_name      = $this->get_meta( 'last_name' );
		$filename       = sanitize_title( "{$last_name}_{$post_id}" ) . '.xlsx';
		$this->filename = $filename;
		$this->filepath = "{$filedir}/{$filename}";
	}

	/**
	 * Generate file.
	 */
	public function generate() {
		$this->render_header();
		$this->render_avatar();
		$this->render_relatives();
		$this->render_experiences();
		$this->render_educations();
		$this->render_passports();
		$this->render_licenses();
		$this->render_bmi();

		$writer = IOFactory::createWriter( $this->spreadsheet, 'Xlsx' );
		$writer->save( $this->filepath );

		return $this->filepath;
	}

	/**
	 * Render header.
	 */
	public function render_header() {
		$this->cell( 'Y5:AB7', $this->get_meta( 'rank' ) . '-' . $this->post->ID );
		$this->cell( 'K2:O2', $this->get_meta( 'rank' ) );
		$this->cell( 'S2:X2', $this->get_date_meta( 'date_available' ) );
		$this->cell( 'K3:X3', $this->get_meta( 'last_name' ) );
		$this->cell( 'K4:X4', $this->get_meta( 'first_name' ) );
		$this->cell( 'K5:O5', $this->get_date_meta( 'birth_date' ) );
		$this->cell( 'K6:O6', $this->get_meta( 'birth_place' ) );
		$this->cell( 'K7:O7', $this->get_meta( 'nationality' ) );
		$this->cell( 'S7:X7', $this->get_meta( 'marital_status' ) );

		$this->cell( 'D9:N9', $this->get_meta( 'phone' ) );
		$this->cell( 'D10:N10', $this->get_meta( 'phone_2' ) );
		$this->cell( 'D11:N11', $this->get_meta( 'email' ) );
		$this->cell( 'D12:N12', $this->get_meta( 'skype' ) );

		$this->cell( 'R9:AB9', $this->get_meta( 'address' ) );
		$this->cell( 'T10:V10', $this->get_meta( 'zip' ) );
		$this->cell( 'X10:AB10', $this->get_meta( 'city' ) );
		$this->cell( 'T11:V11', $this->get_meta( 'state' ) );
		$this->cell( 'Y11:AB11', $this->get_meta( 'country' ) );

		$this->cell( 'R13:AB13', $this->get_meta( 'address' ) );
		$this->cell( 'T14:V14', $this->get_meta( 'zip' ) );
		$this->cell( 'X14:AB14', $this->get_meta( 'city' ) );
		$this->cell( 'T15:V15', $this->get_meta( 'state' ) );
		$this->cell( 'Y15:AB15', $this->get_meta( 'country' ) );

		$this->cell( 'L60:P60', $this->get_date_format( date( "d-m-Y", time() ) ) );
		$this->cell( 'L121:P121', $this->get_date_format( date( "d-m-Y", time() ) ) );
	}

	/**
	 * Rennder avatar.
	 */
	public function render_avatar() {
		$thumbnail = get_post_thumbnail_id( $this->post->ID );
		$thumbnail = get_attached_file( $thumbnail );
		if ( ! $thumbnail ) {
			return;
		}

		$drawing = new Drawing();
		$drawing->setName( $this->post->post_title );
		$drawing->setPath( $thumbnail );
		$drawing->setCoordinates( 'A1' );
		$drawing->setResizeProportional( true );
		$drawing->setWidth( 157 );
		$drawing->setOffsetY( 16 );
		$drawing->setWorksheet( $this->worksheet );
	}

	/**
	 * Render relatives.
	 */
	public function render_relatives() {
		$relatives = $this->get_meta( 'relatives' );

		if ( empty( $relatives ) ) {
			return;
		}

		$relative = array_shift( $relatives );
		$name     = $this->get_value( $relative, 'first_name' );
		$name    .= ' ' . $this->get_value( $relative, 'last_name' );

		$this->cell( 'D13:N13', $name );
		$this->cell( 'D14:N14', $this->get_value( $relative, 'kin' ) );
		$this->cell( 'D15:N15', $this->get_value( $relative, 'contact' ) );
	}

	/**
	 * Render experiences.
	 */
	public function render_experiences() {
		$experiences = $this->get_meta( 'experiences' );

		if ( empty( $experiences ) ) {
			return;
		}

		$experiences = array_slice( $experiences, 0, 10 );

		// Starting cell.
		$start = 20;

		foreach ( $experiences as $exp ) {
			$next = $start + 1;

			$vessel = implode( ' / ', array_filter( [
				$this->get_value( $exp, 'vessel' ),
				$this->get_value( $exp, 'year_built' ),
				$this->get_value( $exp, 'flag' ),
			] ) );

			$this->cell( "B{$start}:K{$start}", $vessel );
			$this->cell( "L{$start}:M{$next}", $this->get_value( $exp, 'rank' ) );
			$this->cell( "N{$start}:P{$start}", $this->get_date_format( $this->get_value( $exp, 'date_start' ) ) );
			$this->cell( "Q{$start}:S{$start}", $this->get_value( $exp, 'type' ) );
			$this->cell( "T{$start}:U{$start}", $this->get_value( $exp, 'dwt' ) );
			$this->cell( "V{$start}:X{$start}", $this->get_value( $exp, 'engine' ) );
			$this->cell( "Y{$start}:AB{$start}", $this->get_value( $exp, 'crewing_agency' ) );

			$crewing_agency = implode( ' / ', array_filter( [
				$this->get_value( $exp, 'owner' ),
				$this->get_value( $exp, 'owner_country' ),
			] ) );

			$this->cell( "B{$next}:K{$next}", $crewing_agency );
			$this->cell( "N{$next}:P{$next}", $this->get_date_format( $this->get_value( $exp, 'date_end' ) ) );
			$this->cell( "Q{$next}:S{$next}", '0' );
			$this->cell( "T{$next}:U{$next}", $this->get_value( $exp, 'grt' ) );
			$this->cell( "V{$next}:X{$next}", $this->get_value( $exp, 'hp' ) );
			$this->cell( "Y{$next}:AB{$next}", $this->get_value( $exp, 'wage' ) );

			$start += 2;
		}
	}

	/**
	 * Render educations.
	 */
	public function render_educations() {
		$educations = $this->get_meta( 'educations' );

		if ( empty( $educations ) ) {
			return;
		}

		$educations = array_slice( $educations, 0, 3 );

		// Starting cell.
		$start = 42;

		foreach ( $educations as $educ ) {
			$this->cell( "A{$start}:E{$start}", $this->get_value( $educ, 'level' ) );
			$this->cell( "F{$start}:T{$start}", $this->get_value( $educ, 'school' ) );
			$this->cell( "U{$start}:X{$start}", $this->get_value( $educ, 'from' ) );
			$this->cell( "Y{$start}:AB{$start}", $this->get_value( $educ, 'to' ) );

			$start += 1;
		}
	}

	/**
	 * Render passports and VISAs.
	 */
	public function render_passports() {
		$passports = $this->get_meta( 'passports' );

		if ( empty( $passports ) ) {
			$passports = [];
		}

		$visas = $this->get_meta( 'visas' );

		if ( empty( $visas ) ) {
			$visas = [];
		}

		if ( empty( $visas ) && empty( $passports ) ) {
			return;
		}

		// Merge passports and VISAs.
		$passports = array_merge( $passports, $visas );

		// Get first 12 items.
		$passports = array_slice( $passports, 0, 12 );

		// Starting cell.
		$start = 47;

		foreach ( $passports as $passport ) {
			$this->cell( "A{$start}:F{$start}", $this->get_value( $passport, 'type' ) );
			$this->cell( "M{$start}:P{$start}", $this->get_value( $passport, 'num' ) );
			$this->cell( "Q{$start}:T{$start}", $this->get_date_format( $this->get_value( $passport, 'issue_date' ) ) );
			$this->cell( "U{$start}:X{$start}", $this->get_value( $passport, 'issued_by' ) );
			$this->cell( "Y{$start}:AB{$start}", $this->get_date_format( $this->get_value( $passport, 'valid_till' ) ) );

			$start += 1;
		}
	}

	/**
	 * Render licenses.
	 */
	public function render_licenses() {
		$licenses = $this->get_meta( 'licenses' );

		if ( empty( $licenses ) ) {
			return;
		}

		// Get first 31 items.
		$licenses = array_slice( $licenses, 0, 31 );

		// Starting cell.
		$start = 63;

		foreach ( $licenses as $license ) {
			$this->cell( "A{$start}:L{$start}", $this->get_value( $license, 'name' ) );
			$this->cell( "M{$start}:P{$start}", $this->get_value( $license, 'num' ) );
			$this->cell( "Q{$start}:T{$start}", $this->get_date_format( $this->get_value( $license, 'issue_date' ) ) );
			$this->cell( "U{$start}:X{$start}", $this->get_value( $license, 'issued_by' ) );
			$this->cell( "Y{$start}:AB{$start}", $this->get_date_format( $this->get_value( $license, 'valid_until' ) ) );

			$start += 1;
		}
	}

	/**
	 * Render BMI.
	 */
	public function render_bmi() {
		$this->cell( "A112:E112", $this->get_meta( 'weight' ) );
		$this->cell( "F112:J112", $this->get_meta( 'height' ) );
		$this->cell( "K112:O112", $this->get_meta( 'overall_size' ) );
		$this->cell( "P112:T112", $this->get_meta( 'shoes_size' ) );
		$this->cell( "U112:X112", $this->get_meta( 'hair_color' ) );
		$this->cell( "Y112:AB112", $this->get_meta( 'eyes_color' ) );
	}

	/**
	 * Create cell.
	 */
	public function cell( $range, $value = null, $styles = [] ) {
		$cells = explode( ':', $range );

		$this->worksheet->mergeCells( $range );
		$this->worksheet->setCellValue( $cells[0], $value );
		$this->worksheet->getStyle( $range )->applyFromArray( $styles );
	}

	/**
	 * Get value from array.
	 */
	public function get_value( $source, $key ) {
		return isset( $source[ $key ] ) ? $source[ $key ] : '';
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
		return $this->get_date_format( $value );
	}

	/**
	 * Get formatted date.
	 */
	public function get_date_format( $value ) {
		return ! empty( $value ) ? date( "d-m-Y", strtotime( $value ) ) : null;
	}
}
