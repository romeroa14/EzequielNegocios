@if($type === 'banner')
    <!-- AdSense Banner -->
    <div class="ad-banner my-4">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-5056825959094581"
             data-ad-slot="YOUR_AD_SLOT_HERE"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
@elseif($type === 'sidebar')
    <!-- AdSense Sidebar -->
    <div class="ad-sidebar">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-5056825959094581"
             data-ad-slot="YOUR_SIDEBAR_AD_SLOT_HERE"
             data-ad-format="auto"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
@elseif($type === 'in-article')
    <!-- AdSense In-Article -->
    <div class="ad-in-article my-4">
        <ins class="adsbygoogle"
             style="display:block; text-align:center;"
             data-ad-layout="in-article"
             data-ad-format="fluid"
             data-ad-client="ca-pub-5056825959094581"
             data-ad-slot="YOUR_IN_ARTICLE_AD_SLOT_HERE"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
@endif