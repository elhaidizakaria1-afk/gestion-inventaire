<?php
function icon_svg(string $name): string
{
    $icons = [
        'logo' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10l5 9-5 9H7l-5-9z"></path></svg>',
        'add' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>',
        'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>',
        'list' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13"></path><circle cx="4" cy="6" r="1"></circle><circle cx="4" cy="12" r="1"></circle><circle cx="4" cy="18" r="1"></circle></svg>',
        'edit' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="m16.5 3.5 4 4L8 20l-5 1 1-5Z"></path></svg>',
        'trash' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path></svg>',
        'box' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 7 9-4 9 4-9 4-9-4Z"></path><path d="m3 7 9 4 9-4"></path><path d="M3 7v10l9 4 9-4V7"></path></svg>',
        'question' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4"></path><circle cx="12" cy="17" r="1"></circle></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 5 5 9-9"></path></svg>',
        'x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"></path></svg>',
        'arrow-left' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg>',
        'arrow-right' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>',
    ];

    $svg = $icons[$name] ?? '';
    if ($svg === '') {
        return '';
    }

    return '<span class="icon-svg icon-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">' . $svg . '</span>';
}
