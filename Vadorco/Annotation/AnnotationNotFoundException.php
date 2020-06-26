<?php
namespace Vadorco\Annotation;

use Vadorco\Core\Persistence\PersistenceResolvingException;

/**
 * Class AnnotationNotFoundException
 * @package Vadorco\Annotation
 *
 * Represents an exception that is thrown when required annotation is not assigned to the class or a class' field.
 */
class AnnotationNotFoundException extends PersistenceResolvingException
{

}
