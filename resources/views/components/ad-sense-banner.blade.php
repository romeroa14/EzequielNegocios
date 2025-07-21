@if($type === 'banner')
    <!-- AdSense Banner - Anuncios Automáticos -->
    <div class="ad-banner my-4">
        <!-- Google AdSense se encargará automáticamente de mostrar anuncios aquí -->
        <!-- No necesitas data-ad-slot con Anuncios Automáticos -->
    </div>
@elseif($type === 'sidebar')
    <!-- AdSense Sidebar - Anuncios Automáticos -->
    <div class="ad-sidebar">
        <!-- Google AdSense se encargará automáticamente de mostrar anuncios aquí -->
        <!-- No necesitas data-ad-slot con Anuncios Automáticos -->
    </div>
@elseif($type === 'in-article')
    <!-- AdSense In-Article - Anuncios Automáticos -->
    <div class="ad-in-article my-4">
        <!-- Google AdSense se encargará automáticamente de mostrar anuncios aquí -->
        <!-- No necesitas data-ad-slot con Anuncios Automáticos -->
    </div>
@endif