<?php
namespace Test\Annotation;

/**
 * @Column(table-name)
 */
class ValidMockAnnotatedClass
{
    /**
     * @Column(column-name)
     * @Type(varchar)
     * @PrimaryKey
     * @NotNull
     */
    private $mock_column = "TEST";
}
