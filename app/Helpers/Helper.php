<?php

namespace Naeem\Helpers;

use Carbon\Carbon;

class Helper
{
	/*
	*  File/Document Uploading
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/

	public static function uploadDocument($file, $path, $filename)
	{
		$extension = $file->getClientOriginalExtension();
		$file->move($path, $filename . '.' . $extension);
		return $path . '/' . $filename . '.' . $extension;
	}

	/*
	*  Get Differences of Days between Two Dates
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function getNoOfDays($stDate, $enDate)
	{
		$stDate = Carbon::parse($stDate)->startOfDay();
		$enDate = Carbon::parse($enDate)->endOfDay();
		return ($enDate->diff($stDate)->format('%a') + 1);
	}

	/*
	*  Get Gender Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_gender($id = 0)
	{

		$data = array(
			'0' => 'Select Gender',
			'1' => 'Male',
			'2' => 'Female'
		);

		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Result of selected text
	*  @access private
	*  @param  array $data, int $id
	*  @return string/array
	*/
	private static function _get_result($data, $id)
	{
		if ($id > 0)
			if (isset($data[$id]))
				return $data[$id];
			else
				return '-';
		else
			return $data;
	}

	/*
	*  Get Marital Status Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_marital_status($id = 0)
	{

		$data =  array(
			'0' => 'Select Status',
			'1' => 'Married',
			'2' => 'Unmarried',
			'3' => 'Divorce',
			'4' => 'Widow',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Religion Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_religion($id = 0)
	{

		$data =  array(
			'0' => 'Select Religion',
			'1' => 'Islam',
			'2' => 'Hindu',
			'3' => 'Christan',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Domicile Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_domicile($id = 0)
	{

		$data =  array(
			'0' => 'Select Domicile',
			'1' => 'KPK',
			'2' => 'Punjab',
			'3' => 'Sindh',
			'4' => 'Balochistan',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Degree Type Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_degree_type($id = 0)
	{

		$data =  array(
			'' => 'Select Degree',
			'0' => 'No Education',
			'1' => 'Middle',
			'2' => 'Matric/O-Level',
			'3' => 'FA/FSc/A-Level',
			'4' => 'Diploma',
			'5' => 'BA/BSc',
			'6' => 'Professional Certification',
			'7' => 'Master/Bacholar Honors',
			'8' => 'MS/M.Phil',
			'9' => 'PhD',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Language Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_language($id = 0)
	{

		$data =  array(
			'' => 'Select Language',
			'0' => 'English',
			'1' => 'Urdu',
			'2' => 'Punjabi',
			'3' => 'Pashto',
			'4' => 'Sindhi',
			'5' => 'Farsi',
			'6' => 'Hindko',
			'7' => 'Sarayiki',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Grade BS Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_grade_bs($id = 0)
	{

		$data =  array(
			'' => 'Select BS',
			'0' => 'No BS',
			'1' => 'BS 11',
			'2' => 'BS 12',
			'3' => 'BS 13',
			'4' => 'BS 14',
			'5' => 'BS 15',
			'6' => 'BS 16',
			'7' => 'BS 17',
			'8' => 'BS 18',
			'9' => 'BS 19',
			'10' => 'BS 20',
			'11' => 'BS 21',
			'12' => 'BS 22',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Strength Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_strength($id = 0)
	{

		$data =  array(
			'' => 'Select Strength',
			'0' => 'Regular',
			'1' => 'Contract',
			'2' => 'Daily Wages',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Get Region Text
	*
	*  @access public
	*  @param  int $id
	*  @return array
	*/
	public static function get_region($id = 0)
	{

		$data =  array(
			'' => 'Select Region',
			'0' => 'KPK',
			'1' => 'Punjab',
			'2' => 'Sindh',
			'3' => 'Balochistan',
		);
		// for selected value
		return Helper::_get_result($data, $id);
	}

	/*
	*  Convert Date Format
	*
	*  @access public
	*  @param  string $date
	*  @return string
	*/
	public static function convert_date($date = 0)
	{

		if ($date == '01-01-1970' || $date == '' || $date == 'dd-mm-yyyy' || $date == null)
			$result = null;
		else
			$result = date('Y-m-d', strtotime($date));

		return $result;
	}

	/*
    *  Get Date Format
    *
    *  @access public
    *  @param  string $date
    *  @return string
    */
	public static function get_date_format($date = 0)
	{

		if ($date == '01-01-1970' || $date == '' || $date == 'dd-mm-yyyy' || $date == null || $date == '1970-01-01 00:00:00')
			$result = null;
		else
			$result = date('d-m-Y', strtotime($date));

		return $result;
	}

	/*
    *  Get Date Format
    *
    *  @access public
    *  @param  string $date
    *  @return string
    */
	public static function get_date($date = 0)
	{

		if ($date == '' || $date == 'dd-mm-yyyy' || $date == null)
			$result = null;
		else
			$result = date('d-m-Y', strtotime($date));

		return $result;
	}

	/**
	 * Convert in Million - Shakir Hussain
	 */
	function number_conversion($n)
	{
		// first strip any formatting;
		$n = (0 + str_replace(",", "", $n));

		// is this a number?
		if (!is_numeric($n)) return false;

		// Conversion		
		elseif ($n > 1000000) return round(($n / 1000000), 2) . ' million';
		elseif ($n > 1000) return round(($n / 1000), 2) . ' thousand';

		return number_format($n);
	}
}
