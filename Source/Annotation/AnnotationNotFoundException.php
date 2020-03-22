<?php
namespace Source\Annotation;

use Source\Core\PersistenceResolvingException;

/**
 * Class AnnotationNotFoundException
 * @package Source\Annotation
 *
 * Represents an exception that is thrown when required annotation is not assigned to the class or a class' field.
 */
class AnnotationNotFoundException extends PersistenceResolvingException
{

}
