import { AnimatePresence, motion } from "motion/react"
import type { PropsWithChildren } from "react"

function AnimatePresenceSlide({
  step,
  children,
}: PropsWithChildren<{ step: number | string }>) {
  return (
    <div className="overflow-hidden p-4 -m-4">
      <AnimatePresence mode="wait" initial={false}>
        <motion.div
          key={step}
          initial={{ opacity: 0, x: 50 }}
          animate={{ opacity: 1, x: 0 }}
          exit={{ opacity: 0, x: -50 }}
          transition={{ duration: 0.25 }}
        >
          {children}
        </motion.div>
      </AnimatePresence>
    </div>
  )
}

export { AnimatePresenceSlide }
