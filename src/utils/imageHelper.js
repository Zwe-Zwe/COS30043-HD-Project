/**
 * Format image URLs consistently throughout the application
 * @param {string} imageLink - The image path or URL
 * @param {boolean} usePlaceholder - Whether to use a placeholder for missing images
 * @returns {string} Properly formatted image URL
 */
export function getFormattedImageUrl(imageLink, usePlaceholder = true) {
  if (!imageLink && usePlaceholder) {
    return "https://via.placeholder.com/150x200?text=No+Image";
  }

  if (imageLink && !imageLink.startsWith("http")) {
    // Ensure consistent path format
    if (imageLink.startsWith("/")) {
      return `http://localhost:3000${imageLink}`;
    } else {
      return `http://localhost:3000/${imageLink}`;
    }
  }

  return imageLink;
}
