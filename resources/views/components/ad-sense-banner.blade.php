@if($type === 'banner')
    <!-- AdSense Banner -->
    <div class="ad-banner my-4">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-5056825959094581"
             data-ad-slot="REEMPLAZAR_CON_AD_SLOT_BANNER"
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
             data-ad-slot="REEMPLAZAR_CON_AD_SLOT_SIDEBAR"
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
             data-ad-slot="REEMPLAZAR_CON_AD_SLOT_IN_ARTICLE"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
@endif