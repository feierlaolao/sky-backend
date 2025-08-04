<?php

namespace App;

class MyResponse
{

    private function __construct(
        private bool $success,
        private $data,
        private string|null $errorMessage,
        private int|null $errorCode,
        private int|null $pageSize,
        private int|null $current,
        private int|null $total
    ) {}

    public static function getInstance(bool $success = true, $data = null, string|null $errorMessage = null, int|null $errorCode = null, int|null $pageSize = null, int|null $current = null, int|null $total = null)
    {
        return new MyResponse($success, $data,  $errorMessage,  $errorCode,  $pageSize,  $current,  $total);
    }

    public function build()
    {
        $temp = [
            'success' => $this->isSuccess()
        ];
        if (!$this->isSuccess()) {
            if ($this->getErrorMessage() != null) {
                $temp['errorMessage'] = $this->getErrorMessage();
            }

            if ($this->getErrorCode() != null) {
                $temp['errorCode'] = $this->getErrorCode();
            }
        } else {
            if ($this->getData() != null) {
                $temp['data'] = $this->getData();
            }else{
                
            }
        }
        return $temp;
    }

    public function list()
    {
        $temp = $this->build();
        if ($this->getCurrent() != null) {
            $temp['current'] = $this->getCurrent();
        }

        if ($this->getPageSize() != null) {
            $temp['pageSize'] = $this->getPageSize();
        }

        if ($this->getTotal() != null) {
            $temp['total'] = $this->getTotal();
        }
        return $temp;
    }


    public function json()
    {
        return json_encode($this->build());
    }


    /**
     * Get the value of success
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Set the value of success
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of errorMessage
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Set the value of errorMessage
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Get the value of errorCode
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Set the value of errorCode
     */
    public function setErrorCode(?int $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the value of pageSize
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * Set the value of pageSize
     */
    public function setPageSize(?int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * Get the value of current
     */
    public function getCurrent(): ?int
    {
        return $this->current;
    }

    /**
     * Set the value of current
     */
    public function setCurrent(?int $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get the value of total
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * Set the value of total
     */
    public function setTotal(?int $total): self
    {
        $this->total = $total;

        return $this;
    }
}
