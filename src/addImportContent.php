<?php

namespace Drupal\import_taxonomy;

use GuzzleHttp\Exception\RequestException;
use Drupal\taxonomy\Entity\Term;

class addImportContent
{
    public static function addImportContentItem($item, &$context)
    {
        $context['sandbox']['current_item'] = $item;
        $message = 'Creating ' ;
        $results = array();

        create_term($item);
        $context['message'] = $message;
        $context['results'][] = $item;
    }

    public static function addImportContentItemCallback($success, $results, $operations)
    {
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                count($results),
                'One item processed.', '@count items processed.'
            );
        } else {
            $message = t('Finished with an error.');
        }
        //drupal_set_message($message);
    }


}

/**
 * {@inheritdoc}
 */
function create_term($item)
{
    $term = Term::create(array(
        'vid' => 'lieux',
        //'parent' => $item['parent'],
        'name' => $item['name'],
        'langcode' => 'fr',
        'status' => 1
    ));
    $term->save();
}

