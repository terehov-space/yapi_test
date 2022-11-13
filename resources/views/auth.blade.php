<html>
<head></head>
<body>
<script defer>
    if (window.location.hash.length > 0) {
        const test = window.location.hash.slice(1)
        window.location.href = `/auth?${test}`
    }
</script>
</body>
</html>
