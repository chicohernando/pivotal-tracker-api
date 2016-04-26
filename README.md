pivotal-tracker-api
===================

Library that provides a PHP interface to interact with the [PivotalTracker API V5](https://www.pivotaltracker.com/help/api/rest/v5)


Example:

```php
$pivotalTracker =  new \PivotalTrackerV5\Client(  $apiToken , $projectId ) ;

$storyList = $pivotalTracker->getStories( 'label:test')  ;
```

To Add a Story:

```php

$story =  array(
		'name' => 'A Brand New Story',
		'story_type' => 'feature',
		'description' => 'A small description',
		'labels' => array(  
		    array( 
		    	'name' => 'test'  
		    ) 
		 )
); 
$result = $pivotalTracker->addStory( $story )  ;

```
