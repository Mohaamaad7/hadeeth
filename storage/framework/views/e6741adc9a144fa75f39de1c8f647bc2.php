
<?php if(config('services.ga4.id')): ?>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(config('services.ga4.id')); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '<?php echo e(config('services.ga4.id')); ?>');
    </script>
<?php endif; ?>

<?php if(config('services.clarity.id')): ?>
    
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?php echo e(config('services.clarity.id')); ?>");
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/partials/analytics.blade.php ENDPATH**/ ?>