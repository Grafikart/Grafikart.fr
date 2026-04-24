<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
  <channel>
    <title>Grafikart.fr</title>
    <description>Grafikart.fr</description>
    <link>{{ config('app.url') }}</link>
    @foreach($items as $item)
    <item>
      <title><![CDATA[{{ $item->title }}]]></title>
      <link>{{ app_url($item, absolute: true) }}</link>
      <guid>{{ app_url($item, absolute: true) }}</guid>
      <description><![CDATA[{{ \App\Helpers\MarkdownHelper::excerpt($item->content) }}]]></description>
      <pubDate>{{ $item->created_at->toRfc2822String() }}</pubDate>
    </item>
    @endforeach
  </channel>
</rss>
