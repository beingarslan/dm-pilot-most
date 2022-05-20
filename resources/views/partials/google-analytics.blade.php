<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('pilot.GOOGLE_ANALYTICS') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ config('pilot.GOOGLE_ANALYTICS') }}');
</script>
