<?php
/**
 * This file is part of the PivotalTracker API component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license LGPL v3 license <http://www.gnu.org/licenses/lgpl>
 */

namespace PivotalTrackerV5;

/**
 * Simple Pivotal Tracker api client.
 *
 * This class is loosely based on the code from Joel Dare's PHP Pivotal Tracker
 * Class: https://github.com/codazoda/PHP-Pivotal-Tracker-Class
 */
class Client
{
    /**
     * Base url for the PivotalTracker service api.
     */
    const API_URL = 'https://www.pivotaltracker.com/services/v5';

    /**
     * Name of the context project.
     *
     * @var string
     */
    private $project;

    /**
     * Used client to perform rest operations.
     *
     * @var \PivotalTracker\Rest\Client
     */
    private $client;
    /**
     *
     * @param string $apiKey  API Token provided by PivotalTracking
     * @param string $project Project ID
     */
    public function __construct( $apiKey, $project )
    {
        $this->client = new Rest\Client( self::API_URL );
        $this->client->addHeader( 'Content-type', 'application/json' );
        $this->client->addHeader( 'X-TrackerToken',  $apiKey );
        $this->project = $project;
    }

    /**
     * Adds a new story to PivotalTracker and returns the newly created story
     * object.
     *
     * @param array $story
     * @param string $name
     * @param string $description
     * @return object
     */
    public function addStory( array $story  )
    {
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/stories",
                json_encode( $story )
            )
        );
    }

    /**
     * Adds a new epic to PivotalTracker and returns the newly created epic
     * object.
     *
     * @param array $epic
     * @return object
     */
    public function addEpic( array $epic )
    {
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/epics",
                json_encode( $epic )
            )
        );
    }

    /**
     * Update a story on Pivotal Tracker and return the updated object
     *
     * @param integer $storyId The id of the story to update
     * @param array $story The data to update the story with
     *
     * @return stdClass
     */
    public function updateStory($storyId, array $story)
    {
        return json_decode(
            $this->client->put(
                "/projects/{$this->project}/stories/$storyId",
                json_encode($story)
            )
        );
    }

    /**
     * Adds an owner to an existing Pivotal Tracker Story and returns that person
     *
     * @param int $storyId
     * @param int $userId
     * @return stdClass
     */
    public function addOwner ( $storyId, $userId )
    {
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/stories/$storyId/owners",
                json_encode(array('id' => $userId))
            )
        );
    }

    /**
     * Adds a comment to the story and returns the newly created comment object
     *
     * @param integer $storyId
     * @param array $commentParameters
     * @return stdClass
     */
    public function addComment($storyId, array $commentParameters)
    {
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/stories/$storyId/comments",
                json_encode($commentParameters)
            )
        );
    }

    /**
     * Adds a new task with <b>$description</b> to the story identified by the
     * given <b>$storyId</b>.
     *
     * @param integer $storyId
     * @param string $description
     * @return \SimpleXMLElement
     */
    public function addTask( $storyId, $description )
    {
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/stories/$storyId/tasks",
                json_encode( array( 'description' => $description ) )

            )
        );
    }

    /**
     * Adds the given <b>$labels</b> to the story identified by <b>$story</b>
     * and returns the updated story instance.
     *
     * @param integer $storyId
     * @param array $labels
     * @return object
     */
    public function addLabels( $storyId, array $labels )
    {
        return json_decode(
            $this->client->put(
                "/projects/{$this->project}/stories/$storyId",
                json_encode(  $labels )
            )
        );
    }

    /**
     * Returns all stories for the context project.
     *
     * @param array $filter
     * @return object
     */
    public function getStories( $filter = null )
    {
        return json_decode(
            $this->client->get(
                "/projects/{$this->project}/stories",
                $filter ? array( 'filter' => $filter ) : null
            )
        );
    }

    /**
     * Returns an array of projects for the currently authenticated user
     *
     * @return array An array of stdClass objects that represent Projects
     */
    public function getProjects($parameters = array()) {
        $default_parameters = array(
            // https://www.pivotaltracker.com/help/api/rest/v5#Projects for parameter descriptions
           'account_ids' => null,
            // https://www.pivotaltracker.com/help/api/rest/v5#project_resource for list of possible fields
           'fields' => null
        );
        
        $parameters = array_merge($default_parameters, $parameters);
        
        return json_decode($this->client->get("/projects", $parameters));
    }

    /**
     * Returns a list of project members
     *
     * @return array
     */
    public function getMemberships() {
        return json_decode($this->client->get("/projects/{$this->project}/memberships"));
    }

    /**
     * Returns the velocity for the project
     *
     * @return int
     */
    public function getProjectVelocity() {
        $response = json_decode($this->client->get("/projects/{$this->project}", array('fields' => 'current_velocity')));
        return $response->current_velocity;
    }

    /**
     * Returns the details for the project
     *
     * @param int $project_id
     * @param array $parameters
     * @return stdClass
     */
    public function getProject($project_id, $parameters = array()) {
        $default_parameters = array(
            // https://www.pivotaltracker.com/help/api/rest/v5#Project for parameter descriptions
            // https://www.pivotaltracker.com/help/api/rest/v5#project_resource for list of possible fields
            'fields' => null
        );
        
        $parameters = array_merge($default_parameters, $parameters);
        return json_decode($this->client->get("/projects/{$project_id}", $parameters));
    }

    /**
     * Performs a Pivotal Tracker search and returns the raw results
     *
     * @param string $query
     * @return stdClass
     */
    public function search($query) {
        return json_decode($this->client->get("/projects/{$this->project}/search", array('query' => $query)));
    }

    /**
     * Returns the count of unscheduled stories
     *
     * @return int
     */
    public function getIceboxCount() {
        $response = $this->search('state:unscheduled');
        return $response->stories->total_hits;
    }

    /**
     * Returns the current iteration and the stories in the iteration
     *
     * @return stdClass
     */
    public function getCurrentIteration() {
        $response = json_decode($this->client->get("/projects/{$this->project}/iterations", array('scope' => 'current')));
        //return first element of the array, which should be the only element of the array
        return reset($response);
    }

    /**
     * Returns a list of iterations for the project
     *
     * @param array $parameters
     * @return array
     */
    public function getProjectIterations($parameters = array()) {
        $default_parameters = array(
             // https://www.pivotaltracker.com/help/api/rest/v5#Iterations for parameter descriptions
            'scope' => 'done',
            'offset' => null,
            'limit' => 10,
            'label' => null,
             // https://www.pivotaltracker.com/help/api/rest/v5#iteration_resource for list of possible fields
            'fields' => null
        );

        $parameters = array_merge($default_parameters, $parameters);

        return json_decode($this->client->get("/projects/{$this->project}/iterations", $parameters));
    }

    /**
     * Returns information about the iteration for the iteration_id passed in
     *
     * @param int $iteration_id
     * @param array $parameters
     * @return stdClass
     */
    public function getIteration($iteration_id, $parameters = array()) {
        $default_parameters = array(
             // https://www.pivotaltracker.com/help/api/rest/v5#Iterations for parameter descriptions
            'label' => null,
             // https://www.pivotaltracker.com/help/api/rest/v5#iteration_resource for list of possible fields
            'fields' => null
        );

        return json_decode($this->client->get("/projects/{$this->project}/iterations/{$iteration_id}", $parameters));
    }



    /**
     * Returns notifications for the current account
     *
     * @param array $parameters
     * @return stdClass
     */
    public function getMyNotifications($parameters = array()) {
        $default_parameters = array(
             // https://www.pivotaltracker.com/help/api/rest/v5#Notifications for parameter descriptions
            'created_after' => null,
            'updated_after' => null,
            'notification_types' => null,
            'limit' => 1000,
             // https://www.pivotaltracker.com/help/api/rest/v5#notification_resource for list of possible fields
            'fields' => null
        );

        $parameters = array_merge($default_parameters, $parameters);

        return json_decode($this->client->get("/my/notifications", $parameters));
    }

    /**
     * Returns notifications for the $person_id passed in.  It will return for dates since the
     * $since millisecond timstamp passed in.
     *
     * @param int $person_id
     * @param int $since Time in milliseconds since the unix epoch
     * @param array $parameters
     * @return stdClass
     */
    public function getPersonNotificationsSince($person_id, $since, $parameters = array()) {
        $default_parameters = array(
             // https://www.pivotaltracker.com/help/api/rest/v5#Notifications for parameter descriptions
            'notification_types' => null,
            'limit' => 1000,
            'format' => 'millis',
             // https://www.pivotaltracker.com/help/api/rest/v5#notification_resource for list of possible fields
            'fields' => null
        );

        $parameters = array_merge($default_parameters, $parameters);

        return json_decode($this->client->get("/people/{$person_id}/notifications/since/{$since}", $parameters));
    }
}