<?php
namespace Source\Database\Condition;

class SimpleConditionBuilder implements ConditionBuilder
{
    /**
     * @var string A query's fragment that is being built.
     */
    private $condition;

    /**
     * @var array An associative array mapping object's property names to corresponding column names.
     */
    private $column_names;

    /**
     * @param array $column_names An associative array mapping object's property names to corresponding column names.
     */
    public function __construct(array $column_names)
    {
        $this->condition = "";
        $this->column_names = $column_names;
    }

    /**
     * @inheritDoc
     */
    public function property(string $name): ConditionBuilder
    {
        $column_name = $this->column_names[$name];
        $this->append("`$column_name`");

        return $this;
    }

    /**
     * @param string $token A token that will be appended to the query.
     * @return ConditionBuilder
     */
    private function append(string $token): ConditionBuilder
    {
        $this->condition .= $token;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function and(): ConditionBuilder
    {
        return $this->append(" AND ");
    }

    /**
     * @inheritDoc
     */
    public function or(): ConditionBuilder
    {
        return $this->append(" OR ");
    }

    /**
     * @inheritDoc
     */
    public function not(): ConditionBuilder
    {
        return $this->append(" NOT ");
    }

    /**
     * @inheritDoc
     */
    public function like(string $pattern): ConditionBuilder
    {
        $pattern = filter_var($pattern, FILTER_SANITIZE_MAGIC_QUOTES);
        $this->append(" LIKE '$pattern'");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function in(string ...$options): ConditionBuilder
    {
        foreach ($options as $i => $option)
        {
            $options[$i] = filter_var($option, FILTER_SANITIZE_MAGIC_QUOTES);
        }

        $options = implode("', '", $options);
        $this->append(" IN ('$options')");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function between(int $from, int $to): ConditionBuilder
    {
        return $this->append(" BETWEEN '$from' TO '$to'");
    }

    /**
     * @inheritDoc
     */
    public function lt(int $value): ConditionBuilder
    {
        return $this->append(" < '$value'");
    }

    /**
     * @inheritDoc
     */
    public function le(int $value): ConditionBuilder
    {
        return $this->append(" <= '$value'");
    }

    /**
     * @inheritDoc
     */
    public function gt(int $value): ConditionBuilder
    {
        return $this->append(" > '$value'");
    }

    /**
     * @inheritDoc
     */
    public function ge(int $value): ConditionBuilder
    {
        return $this->append(" >= '$value'");
    }

    /**
     * @inheritDoc
     */
    public function eq(string $value): ConditionBuilder
    {
        $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES);
        $this->append(" = '$value'");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ne(string $value): ConditionBuilder
    {
        $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES);
        $this->append(" <> '$value'");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->condition;
    }
}
