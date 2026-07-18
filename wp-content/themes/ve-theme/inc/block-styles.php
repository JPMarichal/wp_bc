<?php

add_action('init', function () {
  if (!function_exists('register_block_style')) {
    return;
  }

  register_block_style('core/quote', [
    'name'         => 'bc-blockquote',
    'label'        => 'BC Blockquote',
    'inline_style' => '.wp-block-quote.is-style-bc-blockquote {
      font-family: "Merriweather", Georgia, "Times New Roman", serif;
      font-style: italic;
      line-height: 1.6;
      border-left: 4px solid #2d5a27;
      padding: 1em 1.5em;
      margin: 1.5em 0;
      background: #faf8f5;
    }
    .wp-block-quote.is-style-bc-blockquote cite {
      display: block;
      margin-top: 0.75em;
      font-size: 0.875rem;
      color: #9a8a7b;
      font-style: normal;
    }',
  ]);

  register_block_style('core/pullquote', [
    'name'         => 'bc-pullquote-styled',
    'label'        => 'BC Pullquote',
    'inline_style' => '.wp-block-pullquote.is-style-bc-pullquote-styled {
      border-top: 3px solid #2d5a27;
      border-bottom: 3px solid #2d5a27;
      padding: 1.5em 2em;
      text-align: center;
      margin: 2em 0;
    }
    .wp-block-pullquote.is-style-bc-pullquote-styled p {
      font-family: "Merriweather", Georgia, "Times New Roman", serif;
      font-style: italic;
      font-size: 1.25rem;
      color: #2d5a27;
    }
    .wp-block-pullquote.is-style-bc-pullquote-styled cite {
      font-size: 0.875rem;
      color: #9a8a7b;
    }',
  ]);

  register_block_style('core/table', [
    'name'         => 'bc-table-bordered',
    'label'        => 'BC Table',
    'inline_style' => '.wp-block-table.is-style-bc-table-bordered {
      border-collapse: collapse;
      width: 100%;
    }
    .wp-block-table.is-style-bc-table-bordered thead {
      background: #2d5a27;
      color: #fff;
    }
    .wp-block-table.is-style-bc-table-bordered th,
    .wp-block-table.is-style-bc-table-bordered td {
      border: 1px solid #ede7db;
      padding: 0.625em 1em;
      text-align: left;
    }
    .wp-block-table.is-style-bc-table-bordered tbody tr:nth-child(even) {
      background: #faf8f5;
    }',
  ]);

  register_block_style('core/code', [
    'name'         => 'bc-code-block',
    'label'        => 'BC Code',
    'inline_style' => '.wp-block-code.is-style-bc-code-block {
      background: #1a110a;
      color: #b8d4b3;
      padding: 1em 1.25em;
      border-radius: 6px;
      font-family: "Cascadia Code", "SF Mono", "Fira Code", "Consolas", "Liberation Mono", monospace;
      font-size: 0.9em;
    }',
  ]);

  register_block_style('core/verse', [
    'name'         => 'bc-verse',
    'label'        => 'BC Escritura',
    'inline_style' => '.wp-block-verse.is-style-bc-verse {
      background: #1a110a;
      color: #b8d4b3;
      padding: 1.25em 1.5em;
      border-radius: 6px;
      font-family: "Cascadia Code", "SF Mono", "Fira Code", "Consolas", "Liberation Mono", monospace;
      font-size: 0.9em;
      line-height: 1.7;
    }',
  ]);

  register_block_style('core/paragraph', [
    'name'         => 'bc-lead',
    'label'        => 'BC Intro',
    'inline_style' => '.is-style-bc-lead {
      font-size: 1.125rem;
      line-height: 1.7;
      color: #2c1f14;
      font-weight: 400;
    }',
  ]);

  register_block_style('core/separator', [
    'name'         => 'bc-separator-dotted',
    'label'        => 'BC Punteado',
    'inline_style' => '.wp-block-separator.is-style-bc-separator-dotted {
      border: none;
      border-top: 2px dotted #ede7db;
      width: 120px;
      margin: 2em auto;
    }',
  ]);
});
