import { getImageUrl } from "@/utils/imageUrl"

// Maps an API product to the shape the storefront cards expect.
export function normalizeProduct(p) {
  return {
    ...p,
    image: getImageUrl(p.image),
    displayPrice: Number(p.final_price || p.min_variant_price || 0) || null,
    originalPrice: Boolean(p.is_on_sale) ? Number(p.min_variant_price || 0) || null : null,
    rating: Number(p.reviews_avg_rating || 0),
    reviews: Number(p.reviews_count || 0),
  }
}
