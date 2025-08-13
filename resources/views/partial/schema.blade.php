<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "@yield('schema_type', 'WebSite')",
    "name": "@yield('schema_name', 'Auctions Clone')",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
