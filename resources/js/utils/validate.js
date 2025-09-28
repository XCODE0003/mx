function validateDomain(domain) {
    const domainRegex = /^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/;

    if (domain.length > 256 || domain.length < 3) {
        return false;
    }

    return domainRegex.test(domain);
}export { validateDomain };
