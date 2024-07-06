export const getSubdomain = () => {
  // Get the hostname from the current URL
  const hostname = window.location.hostname;

  // Split the hostname by dots
  const parts = hostname.split('.');

  // Check if the hostname has at least 3 parts (subdomain, domain, and TLD)
  if (parts.length > 2) {
    return parts[0]; // Return the first part, which is the subdomain
  }

  return null; // Return null if there's no subdomain
}

