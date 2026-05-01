import { router } from "@inertiajs/core"
import {
  QueryClient,
  type UndefinedInitialDataOptions,
  type UseMutationOptions,
  useMutation,
  useQuery,
  useQueryClient,
} from "@tanstack/react-query"
import { useCallback, useMemo } from "react"
import { toast } from "sonner"

export const queryClient = new QueryClient()

export async function apiFetch<T>(
  url: string,
  params?: RequestInit,
): Promise<T> {
  return fetch(url, {
    ...params,
    credentials: "include",
    headers: {
      ...params?.headers,
      "X-Requested-With": "XMLHttpRequest",

      accept: "application/json",
      ...(typeof params?.body === "string"
        ? {
            "content-type": "application/json",
          }
        : {}),
    },
  }).then(async (response) => {
    // Pas de réponse, pas de retour
    if (response.status === 204) {
      return null as unknown as Promise<T>
    }
    // Inertia redirection
    if (response.status === 303) {
      const location = response.headers.get("X-Inertia-Location")
      if (!location) {
        toast.error("Impossible de rediriger X-Inertia-Location attendu")
        return
      }
      // TODO : find a way to trigger an inertia redirection without loading inertia
      router.visit(location, { replace: true })
      return
    }
    // La réponse n'est pas du JSON
    if (!response.headers.get("content-type")?.includes("application/json")) {
      throw new APIError({ message: await response.text() }, response.status)
    }
    if (!response.ok) {
      throw new APIError(await response.json(), response.status)
    }
    return response.json()
  })
}

/**
 * Ajoute des les paramètres dans l'URL
 */
function urlWithQueryParams(url: string, params?: Record<string, unknown>) {
  if (!params) {
    return url
  }
  const search = Object.entries(params)
    .reduce((acc, [key, value]) => {
      if (value || typeof value === "number") {
        acc.set(key, value.toString())
      }
      return acc
    }, new URLSearchParams())
    .toString()
  return `${url}?${search}`
}

/**
 * Représente une erreur renvoyée par l'API
 */
export class APIError extends Error {
  constructor(
    public data: unknown,
    public status: number,
  ) {
    super()
  }

  get message(): string {
    if (
      this.data &&
      typeof this.data === "object" &&
      "title" in this.data &&
      typeof this.data.title === "string"
    ) {
      return this.data.title
    }
    if (
      this.data &&
      typeof this.data === "object" &&
      "message" in this.data &&
      typeof this.data.message === "string"
    ) {
      return this.data.message
    }
    return "Erreur serveur"
  }

  get errors(): Record<string, string> {
    if (
      !this.data ||
      typeof this.data !== "object" ||
      !("violations" in this.data)
    ) {
      return {}
    }
    return this.data.violations as Record<string, string>
  }
}

export function useApiFetch<T = unknown>(
  url: string,
  options: Partial<UndefinedInitialDataOptions<T, APIError>> & {
    query?: Record<string, unknown>
  } = {},
) {
  const { query, ...otherOptions } = options
  if (query) {
    // eslint-disable-next-line react-hooks/immutability
    url = urlWithQueryParams(url, query)
  }
  const queryKey = useMemo(() => [url], [url])
  const queryClient = useQueryClient()
  return {
    setData: useCallback(
      (data: T | ((a: T) => T)) => {
        // @ts-expect-error data is too dynamic here
        queryClient.setQueryData<T>(queryKey, data)
      },
      [queryKey, queryClient],
    ),
    ...useQuery<T, APIError>({
      ...otherOptions,
      queryKey,
      queryFn: () => apiFetch<T>(url),
    }),
  }
}

export function useApiMutation<
  T = unknown,
  Body = Record<string, unknown> | Record<string, unknown>[] | FormData | void,
>(
  url: string,
  params: { method: RequestInit["method"] } = { method: "POST" },
  options: UseMutationOptions<T, APIError, Body, unknown> = {},
) {
  const mutation = useMutation<T, APIError, Body>({
    mutationFn: async (payload?: Body) => {
      const isFormData = payload instanceof FormData
      if (isFormData) {
        payload.append("_method", params.method ?? "POST")
      }
      return apiFetch<T>(url, {
        ...params,
        method: isFormData ? "POST" : params.method,
        body: payload instanceof FormData ? payload : JSON.stringify(payload),
      })
    },
    ...options,
  })
  return {
    ...mutation,
    error: mutation.error?.message,
    errors: mutation.error?.errors,
  }
}
