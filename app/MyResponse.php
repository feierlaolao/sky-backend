<?php

namespace App;

use Hyperf\Contract\LengthAwarePaginatorInterface;

class MyResponse
{

    private function __construct(
        private readonly bool    $success,
        private readonly mixed   $data = null,
        private readonly ?string $errorMessage = null,
        private readonly ?int    $errorCode = null,
        private readonly ?int    $pageSize = null,
        private readonly ?int    $current = null,
        private readonly ?int    $total = null
    )
    {
    }

    public static function success(mixed $data = null): MyResponse
    {
        return new self(true, $data);
    }

    public static function error(string $errorMessage, ?int $errorCode = null): MyResponse
    {
        return new self(false, errorMessage: $errorMessage, errorCode: $errorCode);
    }

    public static function page(iterable $data, int $current, int $pageSize, int $total): MyResponse
    {
        return new self(true, data: $data, pageSize: $pageSize, current: $current, total: $total);
    }


    public static function formPaginator(LengthAwarePaginatorInterface $paginator): MyResponse
    {
        return self::page($paginator->items(), $paginator->currentPage(), $paginator->perPage(), $paginator->total());
    }

    public function toArray(): array
    {
        $base = ['success' => $this->success];

        if ($this->success) {
            if ($this->data !== null) {
                $base['data'] = $this->data;
            }
            // 分页信息仅在成功时输出
            $base += array_filter([
                'current' => $this->current,
                'pageSize' => $this->pageSize,
                'total' => $this->total,
            ], static fn($v) => $v !== null);
        } else {
            $base += array_filter([
                'errorMessage' => $this->errorMessage,
                'errorCode' => $this->errorCode,
            ], static fn($v) => $v !== null);
        }

        if (!empty($this->extra)) {
            // 合并额外字段；如与内置键冲突，会被 extra 覆盖
            $base = array_merge($base, $this->extra);
        }

        return $base;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function json(int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->toArray(), $flags);
    }

}
