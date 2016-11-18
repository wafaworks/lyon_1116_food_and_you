<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormInterface;

/**
 * @author Alexander Gorelov
 * @author Alexandru Benzari <benzari.alex@gmail.com>
 * @url https://gist.github.com/Graceas/6505663
 */
class FormErrorSerializer
{
    /**
     * @param FormInterface $form
     * @param bool $flat_array
     * @param bool $add_form_name
     * @param string $glue_keys
     * @return array
     */
    public function serializeFormErrors(
        FormInterface $form,
        $flat_array = true,
        $add_form_name = true,
        $glue_keys = '_'
    ) {
        $errors = array();
        $errors['global'] = array();
        $errors['fields'] = array();

        foreach ($form->getErrors() as $error) {
            $errors['global'][] = $error->getMessage();
        }

        $errors['fields'] = $this->serialize($form);

        if ($flat_array) {
            $errors['fields'] = $this->arrayFlatten(
                $errors['fields'],
                $glue_keys,
                (($add_form_name) ? $form->getName() : '')
            );
        }


        return $errors;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function serialize(FormInterface $form)
    {
        $local_errors = array();
        foreach ($form->all() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $local_errors[$key] = $error->getMessage();
            }

            if (count($child->all()) > 0 && ($child instanceof FormInterface)) {
                $local_errors[$key] = $this->serialize($child);
            }
        }

        return $local_errors;
    }

    /**
     * @param $array
     * @param string $separator
     * @param string $flattened_key
     * @return array
     */
    private function arrayFlatten($array, $separator = "_", $flattened_key = '')
    {
        $flattenedArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->arrayFlatten(
                        $value,
                        $separator,
                        (strlen($flattened_key) > 0 ? $flattened_key . $separator : "") . $key
                    )
                );
            } else {
                $flattenedArray[(strlen($flattened_key) > 0 ? $flattened_key . $separator : "") . $key] = $value;
            }
        }

        return $flattenedArray;
    }
}
