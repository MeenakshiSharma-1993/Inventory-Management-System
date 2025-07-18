<?php

// File generated from our OpenAPI spec

namespace Stripe\Service;

/**
 * @phpstan-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 *
 * @psalm-import-type RequestOptionsArray from \Stripe\Util\RequestOptions
 */
class CouponService extends AbstractService
{
    /**
     * Returns a list of your coupons.
     *
     * @param null|array{created?: array|int, ending_before?: string, expand?: string[], limit?: int, starting_after?: string} $params
     * @param null|RequestOptionsArray|\Stripe\Util\RequestOptions $opts
     *
     * @return \Stripe\Collection<\Stripe\Coupon>
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/coupons', $params, $opts);
    }

    /**
     * You can create coupons easily via the <a
     * href="https://dashboard.stripe.com/coupons">coupon management</a> page of the
     * Stripe dashboard. Coupon creation is also accessible via the API if you need to
     * create coupons on the fly.
     *
     * A coupon has either a <code>percent_off</code> or an <code>amount_off</code> and
     * <code>currency</code>. If you set an <code>amount_off</code>, that amount will
     * be subtracted from any invoice’s subtotal. For example, an invoice with a
     * subtotal of <currency>100</currency> will have a final total of
     * <currency>0</currency> if a coupon with an <code>amount_off</code> of
     * <amount>200</amount> is applied to it and an invoice with a subtotal of
     * <currency>300</currency> will have a final total of <currency>100</currency> if
     * a coupon with an <code>amount_off</code> of <amount>200</amount> is applied to
     * it.
     *
     * @param null|array{amount_off?: int, applies_to?: array{products?: string[]}, currency?: string, currency_options?: array<string, array{amount_off: int}>, duration?: string, duration_in_months?: int, expand?: string[], id?: string, max_redemptions?: int, metadata?: null|array<string, string>, name?: string, percent_off?: float, redeem_by?: int, script?: array{configuration: array, id: string}} $params
     * @param null|RequestOptionsArray|\Stripe\Util\RequestOptions $opts
     *
     * @return \Stripe\Coupon
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/coupons', $params, $opts);
    }

    /**
     * You can delete coupons via the <a
     * href="https://dashboard.stripe.com/coupons">coupon management</a> page of the
     * Stripe dashboard. However, deleting a coupon does not affect any customers who
     * have already applied the coupon; it means that new customers can’t redeem the
     * coupon. You can also delete coupons via the API.
     *
     * @param string $id
     * @param null|array $params
     * @param null|RequestOptionsArray|\Stripe\Util\RequestOptions $opts
     *
     * @return \Stripe\Coupon
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/coupons/%s', $id), $params, $opts);
    }

    /**
     * Retrieves the coupon with the given ID.
     *
     * @param string $id
     * @param null|array{expand?: string[]} $params
     * @param null|RequestOptionsArray|\Stripe\Util\RequestOptions $opts
     *
     * @return \Stripe\Coupon
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/coupons/%s', $id), $params, $opts);
    }

    /**
     * Updates the metadata of a coupon. Other coupon details (currency, duration,
     * amount_off) are, by design, not editable.
     *
     * @param string $id
     * @param null|array{currency_options?: array<string, array{amount_off: int}>, expand?: string[], metadata?: null|array<string, string>, name?: string} $params
     * @param null|RequestOptionsArray|\Stripe\Util\RequestOptions $opts
     *
     * @return \Stripe\Coupon
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     */
    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/coupons/%s', $id), $params, $opts);
    }
}
