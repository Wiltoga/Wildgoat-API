<?php
class cell
{
    /**
     * Constructor
     * 
     * @param int $row_span number of rows used for this cell
     * 
     * @param int $col_span number of columns used for this cell
     * 
     * @param array $content_lines lines of text of this cell
     * 
     * the content is an array of strings
     * 
     * @param array $color color code of the text of this cell
     * 
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     * 
     * @param array $background_color color code of the background of this cell
     * 
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     */
    public function __construct($col_span, $row_span, $content_lines, $color, $background_color)
    {
        $this->row_span = $row_span;
        $this->col_span = $col_span;
        $this->content_lines = $content_lines;
        $this->color = $color;
        $this->background_color = $background_color;
    }
    /**
     * number of rows used for this cell
     * @var int
     */
    public $row_span;
    /**
     * number of columns used for this cell
     * @var int
     */
    public $col_span;
    /**
     * lines of text of this cell
     * 
     * the content is an array of strings
     * 
     * the array is following this format :
     * ```
     *  array(
     *     first_line,
     *     second_line,
     *     third_line
     * );
     * ```
     * 
     * @var array
     */
    public $content_lines;
    /**
     * color code of the text of this cell
     * 
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     * 
     * @var array
     */
    public $color;
    /**
     * color code of the background of this cell
     * 
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     * 
     * @var array
     */
    public $background_color;
}


class table
{
    private $cells;
    /**
     * color code of the background of the table
     * 
     * default : [255, 255, 255]
     * 
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     * 
     * @var array
     */
    public $background_color;
    /**
     * color code of the lines of the table
     * 
     * default : [0, 0, 0]
     * 
     * has no effect for the rows or columns if they are respectively disabled
     *
     * the array is following this format :
     * ```
     *  array(
     *     red,
     *     green,
     *     blue
     * );
     * ```
     * 
     * @var array
     */
    public $lines_color;
    /**
     * to display the vertical lines of each column in the table
     * 
     * default : True
     * 
     * @var bool
     */
    public $display_rows;
    /**
     * to display the horizontal lines of each row in the table
     * 
     * default : True
     * 
     * @var bool
     */
    public $display_columns;
    /**
     * minimum size of the cells 
     * 
     * default : [10, 10]
     * 
     * the array is following this format :
     * ```
     *  array(
     *     min_column_size,
     *     min_row_size
     * );
     * ```
     * 
     * @var array
     */
    public $min_cell_size;
    /**
     * path to the font family
     * 
     * @var string
     */
    public $font_family_file;
    /**
     * size in points of the text
     * 
     * default : 12
     *
     * @var float
     */
    public $font_size;
    /**
     * space around the text of the cells
     * 
     * default : 5
     * 
     * @var int
     */
    public $cell_padding;
    public function __construct()
    {
        $this->cells = [];
        $this->cell_padding = 5;
        $this->font_size = 12;
        $this->min_cell_size = [10, 10];
        $this->background_color = [255, 255, 255];
        $this->lines_color = [0, 0, 0];
        $this->display_rows = true;
        $this->display_columns = true;
    }
    /**
     * puts a cell into the table
     * 
     * @param cell $cell cell to put in the table
     * 
     * @param int $column column of the cell
     * 
     * @param int $row row of the cell
     */
    public function set_cell($cell, $column, $row)
    {
        $this->cells[] = [$cell, $column, $row];
    }
    private function get_sizes()
    {
        $col_width = [];
        $row_height = [];
        $text_width = [];
        $text_height = [];
        $last_column = 0;
        $last_row = 0;
        foreach ($this->cells as [$cell, $column, $row]) {
            if ($last_column < $column + $cell->col_span - 1)
                $last_column = $column + $cell->col_span - 1;
            if ($last_row < $row + $cell->row_span - 1)
                $last_row = $row + $cell->row_span - 1;
            $nb_lines = count($cell->content_lines);
            $max_width = 0;
            $text_width[$column * 4096 + $row] = [];
            foreach ($cell->content_lines as $line) {
                $pts = imagettfbbox($this->font_size, 0, $this->font_family_file, $line);
                $width = ceil($pts[2] - $pts[0]);
                $text_width[$column * 4096 + $row][] = $width;
                if ($max_width < $width)
                    $max_width = $width;
            }
            if (isset($col_width[$column])) {
                if ($col_width[$column] < $max_width + 2 * $this->cell_padding)
                    $col_width[$column] = $max_width + 2 * $this->cell_padding;
            } else
                $col_width[$column] = $max_width + 2 * $this->cell_padding;
            if ($cell->col_span > 1)
                if (!isset($col_width[$column + $cell->col_span - 1])) {
                    $col_width[$column + $cell->col_span - 1] = max(2 * $this->cell_padding, $this->min_cell_size[0]);
                    $text_height[($column + $cell->col_span - 1) * 4096 + $row] = 0;
                }
            if ($cell->row_span > 1)
                if (!isset($row_height[$row + $cell->row_span - 1])) {
                    $row_height[$row + $cell->row_span - 1] = max(2 * $this->cell_padding, $this->min_cell_size[1]);
                    $text_height[$column * 4096 + $row + $cell->row_span - 1] = 0;
                }
            if (isset($row_height[$row])) {
                if ($nb_lines > 0) {
                    $t_height = ceil($this->font_size + ($nb_lines - 1) * $this->font_size * 1.3);
                    if (!isset($text_height[$column * 4096 + $row]))
                        $text_height[$column * 4096 + $row] = 0;
                    if ($text_height[$column * 4096 + $row] < $t_height)
                        $text_height[$column * 4096 + $row] = $t_height;
                    $r_height = ceil($this->font_size + ($nb_lines - 1) * $this->font_size * 1.3 + 2 * $this->cell_padding);
                    if ($row_height[$row] < $r_height)
                        $row_height[$row] = $r_height;
                } else {
                    $row_height[$row] = 2 * $this->cell_padding;
                    $text_height[$column * 4096 + $row] = 0;
                }
            } else {
                if ($nb_lines > 0) {
                    $text_height[$column * 4096 + $row] = ceil($this->font_size + ($nb_lines - 1) * $this->font_size * 1.3);
                    $row_height[$row] = ceil($this->font_size + ($nb_lines - 1) * $this->font_size * 1.3 + 2 * $this->cell_padding);
                } else {
                    $row_height[$row] = 2 * $this->cell_padding;
                    $text_height[$column * 4096 + $row] = 0;
                }
            }
            if ($row_height[$row] < $this->min_cell_size[1])
                $row_height[$row] = $this->min_cell_size[1];
        }
        for ($i = 0; $i < $last_column; $i++)
            if (!isset($col_width[$i]))
                $col_width[$i] = $this->min_cell_size[0];
        for ($i = 0; $i < $last_row; $i++)
            if (!isset($row_height[$i]))
                $row_height[$i] = $this->min_cell_size[1];

        return [$col_width, $row_height, $text_width, $text_height];
    }
    public function generate_image()
    {
        try {
            [$columns_width, $rows_height, $texts_width, $texts_height] = $this->get_sizes();
            $total_width = 0;
            $total_height = 0;
            foreach ($columns_width as $cell_width)
                $total_width += $cell_width;
            foreach ($rows_height as $cell_height)
                $total_height += $cell_height;

            $columns_position = [];
            $curr_width = 0;
            for ($i = 0; $i < count($columns_width); $i++) {
                $columns_position[$i] = $curr_width;
                $curr_width += $columns_width[$i];
            }

            $rows_position = [];
            $curr_height = 0;
            for ($i = 0; $i < count($rows_height); $i++) {
                $rows_position[$i] = $curr_height;
                $curr_height += $rows_height[$i];
            }

            $image = imagecreatetruecolor($total_width, $total_height);
            $background = imagecolorallocate(
                $image,
                $this->background_color[0],
                $this->background_color[1],
                $this->background_color[2]
            );
            imagefilledrectangle($image, 0, 0, intval($total_width), intval($total_height), $background);
            $line = imagecolorallocate(
                $image,
                $this->lines_color[0],
                $this->lines_color[1],
                $this->lines_color[2]
            );
            if ($this->display_columns) {
                imageline($image, $total_width, 0, $total_width, intval($total_height), $line);
                for ($i = 0; $i < count($columns_position); $i++) {
                    imageline($image, intval($columns_position[$i]), 0, intval($columns_position[$i]), intval($total_height), $line);
                }
            }
            if ($this->display_rows) {
                imageline($image, 0, $total_height, $total_width, $total_height, $line);
                for ($i = 0; $i < count($rows_position); $i++)
                    imageline($image, 0, intval($rows_position[$i]), intval($total_width), intval($rows_position[$i]), $line);
            }
            foreach ($this->cells as [$cell, $column, $row]) {
                $cell_background = imagecolorallocate(
                    $image,
                    $cell->background_color[0],
                    $cell->background_color[1],
                    $cell->background_color[2]
                );
                $cell_width = 0;
                for ($i = $column; $i < $column + $cell->col_span; $i++)
                    $cell_width += $columns_width[$i];
                $cell_height = 0;
                for ($i = $row; $i < $row + $cell->row_span; $i++)
                    $cell_height += $rows_height[$i];
                $cell_x = $columns_position[$column];
                $cell_y = $rows_position[$row];
                imagefilledrectangle($image, $cell_x, $cell_y, $cell_x + $cell_width, $cell_y + $cell_height, $cell_background);
                imageline($image, $cell_x, $cell_y, $cell_x + $cell_width, $cell_y, $line);
                imageline($image, $cell_x + $cell_width, $cell_y, $cell_x + $cell_width, $cell_y + $cell_height, $line);
                imageline($image, $cell_x + $cell_width, $cell_y + $cell_height, $cell_x, $cell_y + $cell_height, $line);
                imageline($image, $cell_x, $cell_y + $cell_height, $cell_x, $cell_y, $line);
                $text_pos_y = intval(round($cell_y + $cell_height / 2 - $texts_height[$column * 4096 + $row] / 2));
                for ($i = 0; $i < count($cell->content_lines); $i++) {
                    $text_pos_x = intval(round($cell_x + $cell_width / 2 - $texts_width[$column * 4096 + $row][$i] / 2));
                    $color = imagecolorallocate(
                        $image,
                        $cell->color[0],
                        $cell->color[1],
                        $cell->color[2]
                    );
                    imagettftext($image, $this->font_size, 0, $text_pos_x, $text_pos_y + $this->font_size, $color, $this->font_family_file, $cell->content_lines[$i]);
                    $text_pos_y += $this->font_size * 1.3;
                }
            }

            return $image;
        } catch (Exception $e) {
            echo "$e<br />";
        }
    }
}
