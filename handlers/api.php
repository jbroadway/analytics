<?php

/**
 * Calls the JSON API for the analytics app.
 */

$this->require_admin ();

$this->restful (new analytics\API);

?>