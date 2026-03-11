const PLACEHOLDER = 'https://via.placeholder.com/300x400'

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
