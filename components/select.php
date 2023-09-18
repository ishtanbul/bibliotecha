<?php

class SelectComponent
{

    private array $options;
    private array | null $preselected_options;

    private string $label_index_name;

    private string $value_index_name;

    private bool $is_multiselect;

    private string $id;

    private string $name;

    private int $size;

    private array | string | null $class_list;


    public function __construct(
        string $id,
        array | string | null $class_list = null,
        string $name,
        array $options,
        array | null $preselected_options = null,
        string $label_index_name,
        string $value_index_name = "id",
        bool $is_multiselect = false,
        int $size = 5
    ) {
        $this->options = $this->is_dda($options) ? $options : array($options);
        $this->preselected_options = is_null($preselected_options) ? null : ($this->is_dda($preselected_options) ?  $preselected_options :  array($preselected_options));
        $this->label_index_name = $label_index_name;
        $this->value_index_name = $value_index_name;
        $this->is_multiselect = $is_multiselect;
        $this->id = $id;
        $this->name = $name;
        $this->size = $size;
        $this->class_list = $class_list;
    }

    private function class_list_to_string(): string
    {
        if (is_array($this->class_list)) {
            return implode(" ", $this->class_list);
        }
        if (is_string($this->class_list)) {
            return $this->class_list;
        }
        return "";
    }

    private function is_dda($arr): bool
    {
        $_0 = array_key_first($arr);
        return is_array($arr) && is_array($arr[$_0]);
    }

    private function render_select_option(array $option): string | int
    {
        if (!is_array($option)) {
            $message = sprintf("Program was expecting the option to be in the form of an array. The following is invalid: %s)", json_encode($option));
            error_log($message);
            return -1;
        }
        if (!array_key_exists($this->label_index_name, $option)) {
            $message = sprintf("Program was expecting the option to have a label index called %s)", $this->label_index_name);
            error_log($message);
            return -1;
        }

        if (!array_key_exists($this->value_index_name, $option)) {
            $message = sprintf("Program was expecting the option to have a label index called %s)", $this->value_index_name);
            error_log($message);
            return -1;
        }

        $label = $option[$this->label_index_name];
        $value = $option[$this->value_index_name];
        $selected_flag = $this->is_option_selected($option);
        $_option_f = $selected_flag ? '<option value="%s" selected>%s</option>' : '<option value="%s">%s</option>';
        $_option =  sprintf($_option_f, $value,  $label);
        return $_option;
    }

    private function render_selected_options(): string
    {
        $options_list = [];
        foreach ($this->options as $option) {
            $_option = $this->render_select_option($option);
            if ($_option != -1) {
                array_push($options_list, $_option);
            }
        }
        $_options = implode("\n", $options_list);
        return $_options;
    }

    public function render_component(): string
    {
        $_select_f = $this->is_multiselect ? '
            <select id="%s" class="%s" multiple name="%s" size="%s">
                %s
            </select>
        ' :
            '<select id="%s" class="%s" name="%s" size="%s">
                %s
            </select>';

        $_select = sprintf($_select_f, $this->id, $this->class_list_to_string(), $this->name, $this->size, $this->render_selected_options());
        return $_select;
    }

    private function is_option_selected($option): bool
    {
        if (is_null($this->preselected_options)) {
            return false;
        }


        $option_label = $option[$this->label_index_name];
        $option_value = $option[$this->value_index_name];

        foreach ($this->preselected_options as $preselected_option) {
            $preselected_option_label = $preselected_option[$this->label_index_name];
            $preselected_option_value = $preselected_option[$this->value_index_name];
            if ($option_label == $preselected_option_label && $option_value == $preselected_option_value) {
                return true;
            }
        }
        return false;
    }
}
