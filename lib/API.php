<?php

namespace analytics;

class API extends \Restful {
	public $email;
	public $pass;
	public $profile;
	public $ga;
	public $err;

	public function __construct () {
		try {
			$settings = parse_ini_file ('conf/analytics.php');
			$this->email = $settings['email'];
			$this->pass = $settings['pass'];
			$this->profile = $settings['profile'];
			$this->ga = new \gapi ($this->email, $this->pass);
		} catch (Exception $e) {
			$this->err = $e->getMessage ();
		}
	}

	/**
	 * Get the general visitor info. Accessed via:
	 *
	 *   /analytics/api/visitors
	 */
	public function get_visitors () {
		if ($this->error) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$data = array (
				(object) array ('label' => i18n_get ('Page views'), 'data' => array ()),
				(object) array ('label' => i18n_get ('Visits'), 'data' => array ()),
			);
	
			$this->ga->requestReportData (
				$this->profile,
				array ('date'),
				array ('pageviews', 'visits', 'visitors', 'avgTimeOnSite'),
				array ('date'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d') // today
			);

			foreach ($this->ga->getResults () as $k => $res) {
				$data[0]->data[] = array ($k, $res->getPageviews ());
				$data[1]->data[] = array ($k, $res->getVisits ());
			}

			return array (
				'data' => $data,
				'visitors' => $this->ga->getVisitors (),
				'visits' => $this->ga->getVisits (),
				'pageviews' => $this->ga->getPageviews (),
				'pagespervisit' => round ($this->ga->getPageviews () / $this->ga->getVisits (), 2),
				'avgtimeonsite' => floor ($this->ga->getAvgTimeOnSite () / 60) . ':' . ($this->ga->getAvgTimeOnSite () % 60)
			);
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top referral sources. Accessed via:
	 *
	 *   /analytics/api/sources
	 */
	public function get_sources () {
		if ($this->error) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('source'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d') // today
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'source' => $res->getSource (),
					'visits' => $res->getVisits ()
				);
			}

			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top search terms. Accessed via:
	 *
	 *   /analytics/api/keywords
	 */
	public function get_keywords () {
		if ($this->error) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('keyword'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d') // today
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'keyword' => $res->getKeyword (),
					'visits' => $res->getVisits ()
				);
			}

			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top landing pages. Accessed via:
	 *
	 *   /analytics/api/landingpages
	 */
	public function get_landingpages () {
		if ($this->error) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('landingPagePath'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d') // today
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'page' => $res->getLandingPagePath (),
					'visits' => $res->getVisits ()
				);
			}

			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top countries. Accessed via:
	 *
	 *   /analytics/api/countries
	 */
	public function get_countries () {
		if ($this->error) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('country'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d') // today
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'country' => $res->getCountry (),
					'visits' => $res->getVisits ()
				);
			}

			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}
}

?>