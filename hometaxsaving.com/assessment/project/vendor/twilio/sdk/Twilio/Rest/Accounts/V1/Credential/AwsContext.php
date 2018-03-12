<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Accounts\V1\Credential;

use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

class AwsContext extends InstanceContext {
    /**
     * Initialize the AwsContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $sid The sid
     * @return \Twilio\Rest\Accounts\V1\Credential\AwsContext 
     */
    public function __construct(Version $version, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array('sid' => $sid);

        $this->uri = '/Credentials/AWS/' . rawurlencode($sid) . '';
    }

    /**
     * Fetch a AwsInstance
     * 
     * @return AwsInstance Fetched AwsInstance
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new AwsInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Update the AwsInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return AwsInstance Updated AwsInstance
     */
    public function update($options = array()) {
        $options = new Values($options);

        $data = Values::of(array('FriendlyName' => $options['friendlyName']));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new AwsInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Deletes the AwsInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Accounts.V1.AwsContext ' . implode(' ', $context) . ']';
    }
}