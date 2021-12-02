<?php
/**
 * @file
 * Contains \Drupal\import_annonce\Form\ImportForm.
 */
namespace Drupal\import_taxonomy\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ImportForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'import_content_csv';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['description'] = array(
      '#markup' => '<p>Use this form to upload a CSV file of Data</p>',
    );

    $form['import_taxonomy'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload File'),
      '#upload_location' => 'public://import_taxonomy/',
      '#required' => TRUE,
      "#upload_validators" => array("file_validate_extensions" => array("csv")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';


    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import Content CSV'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    $csv_file = $form_state->getValue('import_taxonomy');
    $file = File::load($csv_file[0]);
    $file->setPermanent();
    $file->save();
    $data = $this->csvtoarray($file->getFileUri(), ',');
    foreach ($data as $row) {
      //$this->create_node($row);
      $operations[] = ['\Drupal\import_taxonomy\addImportContent::addImportContentItem', [$row]];
    }
    //dsm($data);
    $batch = array(
      'title' => t('Importing Data CSV...'),
      'operations' => $operations,
      'init_message' => t('Import is starting.'),
      'finished' => '\Drupal\import_taxonomy\addImportContent::addImportContentItemCallback',
    );
    batch_set($batch);
  }

  public function csvtoarray($filename = '', $delimiter)
  {

    if (!file_exists($filename) || !is_readable($filename)) return FALSE;
    $header = NULL;
    $data = array();

    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        if (!$header) {
          $header = $row;
        } else {
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }

    return $data;
  }

}
