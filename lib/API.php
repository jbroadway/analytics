<?php

namespace analytics;

class API extends \Restful {
	public $email;
	public $pass;
	public $profile;
	public $token;
	public $ga;
	public $err;

	/**
	 * Authenticates and generates an auth token.
	 */
	public function init () {
		try {
			$settings = parse_ini_file ('conf/analytics.php');
			$this->email = $settings['email'];
			$this->pass = $settings['pass'];
			$this->profile = $settings['profile'];
			$this->token = $_SESSION['ga_auth_token'] ? $_SESSION['ga_auth_token'] : null;

			$this->ga = new \gapi ($this->email, $this->pass, $this->token);

			if ($this->token === null) {
				$this->token = $this->ga->getAuthToken ();
				$_SESSION['ga_auth_token'] = $this->token;
			}
			return true;
		} catch (Exception $e) {
			$this->err = $e->getMessage ();
			return false;
		}
	}

	/**
	 * Get the general visitor info. Accessed via:
	 *
	 *   /analytics/api/visitors
	 */
	public function get_visitors () {
		global $cache;

		$res = $cache->get ('analytics_visitors');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
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

			$res = array (
				'data' => $data,
				'visitors' => $this->ga->getVisitors (),
				'visits' => $this->ga->getVisits (),
				'pageviews' => $this->ga->getPageviews (),
				'pagespervisit' => round ($this->ga->getPageviews () / $this->ga->getVisits (), 2),
				'avgtimeonsite' => floor ($this->ga->getAvgTimeOnSite () / 60) . ':' . ($this->ga->getAvgTimeOnSite () % 60)
			);
			$cache->set ('analytics_visitors', $res, 43200);
			return $res;
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
		global $cache;

		$res = $cache->get ('analytics_sources');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
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
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'source' => $res->getSource (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_sources', $data, 43200);
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
		global $cache;

		$res = $cache->get ('analytics_keywords');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
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
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'keyword' => $res->getKeyword (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_keywords', $data, 43200);
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
		global $cache;

		$res = $cache->get ('analytics_landingpages');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
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
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'page' => $res->getLandingPagePath (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_landingpages', $data, 43200);
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
		global $cache;

		$res = $cache->get ('analytics_countries');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
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
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'country' => $res->getCountry (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_countries', $data, 43200);
			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top pages on the site. Accessed via:
	 *
	 *   /analytics/api/toppages
	 */
	public function get_toppages () {
		global $cache;

		$res = $cache->get ('analytics_toppages');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('pagePath'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'page' => $res->getPagePath (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_toppages', $data, 43200);
			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}

	/**
	 * Get the top browsers used on the site. Accessed via:
	 *
	 *   /analytics/api/browsers
	 */
	public function get_browsers () {
		global $cache;

		$res = $cache->get ('analytics_browsers');
		if ($res) {
			return $res;
		}

		if (! $this->init ()) {
			// failed to connect
			return $this->error ($this->err);
		}

		try {
			$min = time () - 2592000; // 30 days ago
	
			$this->ga->requestReportData (
				$this->profile,
				array ('browser'),
				array ('visits'),
				array ('-visits'), // sort
				'', // filter
				gmdate ('Y-m-d', $min),
				gmdate ('Y-m-d'), // today
				1, 10
			);

			$data = array ();

			foreach ($this->ga->getResults () as $k => $res) {
				$data[] = array (
					'browser' => $res->getBrowser (),
					'visits' => $res->getVisits ()
				);
			}

			$cache->set ('analytics_browsers', $data, 43200);
			return $data;
		} catch (Exception $e) {
			return $this->error ($e->getMessage ());
		}
	}
}

?>