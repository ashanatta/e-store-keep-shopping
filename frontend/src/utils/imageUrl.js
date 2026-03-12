// Inline SVG placeholder - no external requests, always works
const PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='400'%3E%3Crect fill='%23e9ecef' width='300' height='400'/%3E%3Ctext fill='%236c757d' x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='16'%3ENo image%3C/text%3E%3C/svg%3E"

/**
 * Returns the full URL for an image.
 * - If path is already a full URL (e.g. Cloudinary), returns as-is.
 * - Otherwise builds backend /api/files/{path} URL.
 */
export function getImageUrl(path, placeholder = PLACEHOLDER) {
  if (!path) return placeholder
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const base = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/api\/?$/, '')
  return `${base}/api/files/${path}`
}
