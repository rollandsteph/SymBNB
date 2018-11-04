<?php
namespace App\Form\DataTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface {

    /**
     * transforme une date de type datetime en date au format francais
     *
     * @param [type] $date
     * @return void
     */
    public function transform($date){
        if($date === null){
            return '';
        }
        return $date->format('d/m/Y');
    }

    /**
     * transforme une date en francais en un objet datetime
     *
     * @param [type] $frenchDate
     * @return void
     */
    public function reverseTransform($frenchDate){
        if($frenchDate === null){
            // exception
            throw new TransformationFailedException("Vous devez fournir une date");
        }

        $date=\DateTime::createFromFormat('d/m/Y', $frenchDate);
        if($date === false){
            // exception
            throw new TransformationFailedException("Le format de la date n'est pas correct");
        }
        return $date;
    }
}
