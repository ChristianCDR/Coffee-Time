import { useState } from 'react';
import { useToggleMenuReturn } from "@/types/index";

export function useToggleMenu(): useToggleMenuReturn {
    const [open, setOpen] = useState(false);

    const toggleMenu = () => {
        setOpen((prev) => {
            const next = !prev;
            document.body.style.overflow = open ? 'auto' : 'hidden';
            return next;
        });
    };

    return { open, toggleMenu };
}