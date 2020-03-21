<?php
namespace Test\Annotation;

/**
 * @Table(table-name)
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
