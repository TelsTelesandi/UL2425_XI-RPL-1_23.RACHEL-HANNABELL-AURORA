<?php
class TCPDF {
    private $page_width = 297;  // A4 landscape width in mm
    private $page_height = 210; // A4 landscape height in mm
    private $margin = 10;
    private $current_x = 10;
    private $current_y = 10;
    private $font_size = 12;
    private $font_style = '';
    private $content = '';
    private $title = '';
    private $author = '';
    private $creator = '';
    
    public function SetCreator($creator) {
        $this->creator = $creator;
    }
    
    public function SetAuthor($author) {
        $this->author = $author;
    }
    
    public function SetTitle($title) {
        $this->title = $title;
    }
    
    public function AddPage($orientation = 'L') {
        $this->content .= "<div style='page-break-before: always;'></div>\n";
        $this->current_x = $this->margin;
        $this->current_y = $this->margin;
    }
    
    public function SetFont($family, $style = '', $size = 12) {
        $this->font_style = $style;
        $this->font_size = $size;
    }
    
    public function Cell($w, $h, $txt, $border = 0, $ln = 0, $align = '', $fill = false) {
        $style = "display: inline-block; ";
        $style .= "width: {$w}mm; ";
        $style .= "height: {$h}mm; ";
        $style .= "line-height: {$h}mm; ";
        if ($border) {
            $style .= "border: 1px solid black; ";
        }
        if ($align) {
            $style .= "text-align: $align; ";
        }
        if ($this->font_style == 'B') {
            $style .= "font-weight: bold; ";
        }
        $style .= "font-size: {$this->font_size}pt; ";
        
        $this->content .= "<div style='$style'>$txt</div>";
        
        if ($ln) {
            $this->content .= "<br>\n";
            $this->current_x = $this->margin;
            $this->current_y += $h;
        } else {
            $this->current_x += $w;
        }
    }
    
    public function Ln($h = '') {
        $this->content .= "<br>\n";
        $this->current_x = $this->margin;
        if ($h) {
            $this->current_y += $h;
        }
    }
    
    public function Output($name = '', $dest = '') {
        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html>\n";
        echo "<html>\n<head>\n";
        echo "<title>" . htmlspecialchars($this->title) . "</title>\n";
        echo "<style>\n";
        echo "@media print {\n";
        echo "  @page { size: landscape; margin: {$this->margin}mm; }\n";
        echo "  body { font-family: Arial, sans-serif; }\n";
        echo "}\n";
        echo "</style>\n";
        echo "</head>\n<body>\n";
        echo $this->content;
        echo "\n<script>window.print();</script>\n";
        echo "</body>\n</html>";
    }
}
?> 