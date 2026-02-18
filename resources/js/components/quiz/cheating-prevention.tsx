import { motion } from "motion/react"
import { WobblyDialog } from "@/components/ui/wobbly-dialog.tsx"

export function CheatingPrevention() {
  return (
    <motion.div
      className="fixed left-10 bottom-(--drawer-frontmost-height) flex items-end -z-1"
      initial={{ opacity: 0, y: 50 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: 50 }}
      transition={{ duration: 0.25 }}
    >
      <img
        src="/images/illustrations/angry.webp"
        alt=""
        width={659}
        height={854}
        className="h-70 w-auto mascot -z-1"
      />
      <WobblyDialog
        className="mb-20"
        name="Hey !"
        content="Normalement pas besoin de revenir à la vidéo"
      />
    </motion.div>
  )
}
